<?php

namespace App\Services\Payments\Providers;

use App\Services\Payments\Contracts\PaymentGateway;
use App\Services\Payments\DTO\PaymentCallbackResult;
use App\Services\Payments\DTO\PaymentCreationResult;
use App\Services\Payments\Exceptions\PaymentGatewayException;
use Illuminate\Support\Facades\Http;

final class PayPalPaymentGateway implements PaymentGateway
{
    public const PROVIDER = 'PAYPAL';

    public function provider(): string { return self::PROVIDER; }

    public function isConfigured(): bool
    {
        return $this->clientId() !== '' && $this->secret() !== ''
            && filter_var($this->baseUrl(), FILTER_VALIDATE_URL) !== false
;
    }

    public function createPayment(array $context): PaymentCreationResult
    {
        $this->ensureConfigured();
        $vnd = (int) ($context['amount'] ?? 0);
        if ($vnd < 1) throw PaymentGatewayException::invalidRequest(self::PROVIDER, 'Số tiền PayPal không hợp lệ.');

        $paypalAmount = $this->toPaypalAmount($vnd);

        $payload = [
            'intent' => 'CAPTURE',
            'purchase_units' => [[
                'reference_id' => (string) $context['merchant_order_id'],
                'custom_id' => (string) $context['merchant_order_id'],
                'description' => mb_substr((string) $context['order_info'], 0, 127),
                'amount' => ['currency_code' => $this->currency(), 'value' => $paypalAmount],
            ]],
            'payment_source' => ['paypal' => ['experience_context' => [
                'brand_name' => 'GreenShop',
                'shipping_preference' => 'NO_SHIPPING',
                'user_action' => 'PAY_NOW',
                'return_url' => (string) $context['return_url'],
                'cancel_url' => $this->cancelUrl((string) $context['return_url']),
            ]]],
        ];

        try {
            $response = Http::withToken($this->accessToken())->acceptJson()->asJson()
                ->timeout($this->timeout())->post($this->baseUrl().'/v2/checkout/orders', $payload);
        } catch (\Throwable $e) {
            throw PaymentGatewayException::transport(self::PROVIDER, 'Không thể kết nối PayPal.', $e);
        }
        $data = $response->json();
        if (!$response->successful() || !is_array($data) || empty($data['id'])) {
            throw PaymentGatewayException::rejected(
                self::PROVIDER,
                'PayPal không thể tạo đơn thanh toán: '.($data['message'] ?? 'HTTP '.$response->status()),
                $response->status()
            );
        }
        $approve = collect($data['links'] ?? [])->firstWhere('rel', 'payer-action')
            ?? collect($data['links'] ?? [])->firstWhere('rel', 'approve');
        $url = is_array($approve) ? ($approve['href'] ?? null) : null;
        if (!$url) throw PaymentGatewayException::unknown(self::PROVIDER, 'PayPal không trả về đường dẫn thanh toán.');

        return new PaymentCreationResult($url, (string)$context['merchant_order_id'], (string)$context['request_id'], 'CREATED', 'PayPal order created.', $data);
    }

    public function capture(string $paypalOrderId, int $expectedVnd): PaymentCallbackResult
    {
        $this->ensureConfigured();
        $paypalOrderId = trim($paypalOrderId);
        if ($paypalOrderId === '') {
            throw PaymentGatewayException::invalidRequest(self::PROVIDER, 'PayPal không trả về mã đơn thanh toán.');
        }

        try {
            $token = $this->accessToken();
            // PayPal Orders v2 yêu cầu body của capture là một JSON object rỗng `{}`.
            // Http::post(..., []) với asJson() sẽ serialize thành `[]` (JSON array),
            // khiến PayPal trả 400 INVALID_REQUEST vì sai schema.
            $response = Http::withToken($token)
                ->acceptJson()
                ->withBody('{}', 'application/json')
                ->timeout($this->timeout())
                ->post($this->baseUrl().'/v2/checkout/orders/'.rawurlencode($paypalOrderId).'/capture');
            $data = $response->json();

            // Khi người dùng refresh trang return, PayPal có thể trả ORDER_ALREADY_CAPTURED.
            // Lúc đó đọc lại order để xác nhận capture đã hoàn tất thay vì để đơn GreenShop
            // mắc kẹt ở trạng thái chờ thanh toán.
            if (! $response->successful()) {
                $lookup = Http::withToken($token)->acceptJson()->timeout($this->timeout())
                    ->get($this->baseUrl().'/v2/checkout/orders/'.rawurlencode($paypalOrderId));
                if ($lookup->successful() && is_array($lookup->json())) {
                    $data = $lookup->json();
                }
            }
        } catch (\Throwable $e) {
            throw PaymentGatewayException::transport(self::PROVIDER, 'Không thể xác nhận giao dịch PayPal.', $e);
        }

        if (!is_array($data)) {
            throw PaymentGatewayException::unknown(self::PROVIDER, 'PayPal trả về dữ liệu không hợp lệ.');
        }

        $unit = $data['purchase_units'][0] ?? [];
        // PayPal không bảo đảm custom_id luôn xuất hiện trong mọi biến thể response
        // của endpoint capture. reference_id cũng chính là merchant_transaction_id
        // GreenShop gửi lúc tạo order, nên dùng làm fallback để đối soát.
        $merchant = trim((string)($unit['custom_id'] ?? ''));
        if ($merchant === '') {
            $merchant = trim((string)($unit['reference_id'] ?? ''));
        }

        $capture = $unit['payments']['captures'][0] ?? [];
        $paidValue = (string)($capture['amount']['value'] ?? '');
        $currency = strtoupper((string)($capture['amount']['currency_code'] ?? ''));
        $expectedPaypalValue = $this->toPaypalAmount($expectedVnd);
        $completed = strtoupper((string)($data['status'] ?? '')) === 'COMPLETED'
            && strtoupper((string)($capture['status'] ?? '')) === 'COMPLETED';
        $valid = $merchant !== ''
            && $expectedVnd > 0
            && $currency === $this->currency()
            && is_numeric($paidValue)
            && abs((float) $paidValue - (float) $expectedPaypalValue) < 0.011;

        return new PaymentCallbackResult(
            validSignature: $valid,
            successful: $valid && $completed,
            pending: ! $completed,
            merchantOrderId: $merchant,
            providerRequestId: '',
            providerTransactionId: isset($capture['id']) ? (string)$capture['id'] : null,
            amount: $valid ? $expectedVnd : null,
            responseCode: (string)($data['status'] ?? $response->status()),
            message: $completed ? 'Giao dịch PayPal thành công.' : 'PayPal chưa xác nhận thanh toán.',
        );
    }

    public function verifyCallback(array $payload): PaymentCallbackResult
    {
        throw PaymentGatewayException::invalidRequest(self::PROVIDER, 'PayPal được xác minh trực tiếp qua API capture.');
    }

    private function cancelUrl(string $returnUrl): string
    {
        parse_str((string) parse_url($returnUrl, PHP_URL_QUERY), $query);
        return route('thanh-toan.paypal.cancel', ['transaction' => (int)($query['transaction'] ?? 0)]);
    }

    private function accessToken(): string
    {
        $r = Http::withBasicAuth($this->clientId(), $this->secret())->asForm()->acceptJson()->timeout($this->timeout())
            ->post($this->baseUrl().'/v1/oauth2/token', ['grant_type' => 'client_credentials']);
        $token = (string)($r->json('access_token') ?? '');
        if (!$r->successful() || $token === '') throw PaymentGatewayException::configuration(self::PROVIDER, 'Client ID hoặc Secret PayPal không hợp lệ.');
        return $token;
    }
    private function ensureConfigured(): void { if (!$this->isConfigured()) throw PaymentGatewayException::configuration(self::PROVIDER, 'PayPal chưa được cấu hình.'); }
    private function clientId(): string { return trim((string)config('services.paypal.client_id', '')); }
    private function secret(): string { return trim((string)config('services.paypal.secret', '')); }
    private function baseUrl(): string { return rtrim((string)config('services.paypal.base_url', 'https://api-m.sandbox.paypal.com'), '/'); }
    private function timeout(): int { return max(5, (int)config('services.paypal.timeout', 30)); }
    private function currency(): string { return strtoupper((string) config('services.paypal.currency', 'USD')); }
    private function vndPerUsd(): float { return max(1, (float) config('services.paypal.vnd_per_usd', 26000)); }
    private function toPaypalAmount(int $vnd): string
    {
        // PayPal Checkout REST không nhận VND làm payment currency.
        // GreenShop vẫn lưu/hiển thị VND, còn PayPal Sandbox nhận USD.
        return number_format($vnd / $this->vndPerUsd(), 2, '.', '');
    }
}
