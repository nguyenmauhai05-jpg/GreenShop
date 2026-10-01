<?php

namespace App\Services\Payments\Providers;

use App\Models\GiaoDichThanhToan;
use App\Services\Payments\Contracts\PaymentGateway;
use App\Services\Payments\DTO\PaymentCallbackResult;
use App\Services\Payments\DTO\PaymentCreationResult;
use App\Services\Payments\Exceptions\PaymentGatewayException;
use Illuminate\Support\Facades\Http;
use Throwable;

/** Direct payOS REST integration, no third-party PHP package required. */
final class PayOSPaymentGateway implements PaymentGateway
{
    public const PROVIDER = 'PAYOS';

    public function provider(): string { return self::PROVIDER; }

    public function isConfigured(): bool
    {
        return $this->clientId() !== '' && $this->apiKey() !== '' && $this->checksumKey() !== ''
            && filter_var($this->baseUrl(), FILTER_VALIDATE_URL) !== false;
    }

    public function createPayment(array $context): PaymentCreationResult
    {
        if (! $this->isConfigured()) {
            throw PaymentGatewayException::configuration(self::PROVIDER, 'payOS chưa được cấu hình Client ID, API Key và Checksum Key.');
        }

        // PayOS requires a numeric, unique orderCode. Transaction PK already unique.
        $transactionId = (int) ($context['transaction_id'] ?? 0);
        $amount = (int) ($context['amount'] ?? 0);
        if ($transactionId <= 0 || $amount < 2000) {
            throw PaymentGatewayException::invalidRequest(self::PROVIDER, 'payOS yêu cầu mã giao dịch hợp lệ và số tiền tối thiểu 2.000 VND.');
        }
        $returnUrl = (string) ($context['return_url'] ?? '');
        $cancelUrl = (string) ($context['cancel_url'] ?? $returnUrl);
        if (!filter_var($returnUrl, FILTER_VALIDATE_URL) || !filter_var($cancelUrl, FILTER_VALIDATE_URL)) {
            throw PaymentGatewayException::invalidRequest(self::PROVIDER, 'URL trả về payOS không hợp lệ.');
        }
        $description = 'GS'.$transactionId; // Short ASCII transfer description, <= 25 chars.
        $signatureInput = 'amount='.$amount.'&cancelUrl='.$cancelUrl.'&description='.$description
            .'&orderCode='.$transactionId.'&returnUrl='.$returnUrl;
        $payload = [
            'orderCode' => $transactionId,
            'amount' => $amount,
            'description' => $description,
            'cancelUrl' => $cancelUrl,
            'returnUrl' => $returnUrl,
            'expiredAt' => now()->addMinutes(max(5, (int) config('services.payments.ttl_minutes', 15)))->timestamp,
            'signature' => hash_hmac('sha256', $signatureInput, $this->checksumKey()),
        ];
        try {
            $response = Http::withHeaders([
                'x-client-id' => $this->clientId(),
                'x-api-key' => $this->apiKey(),
            ])->acceptJson()->timeout($this->timeout())
                ->post($this->baseUrl().'/v2/payment-requests', $payload);
        } catch (Throwable $e) {
            throw PaymentGatewayException::transport(self::PROVIDER, 'Không thể kết nối payOS; cần đối soát trước khi thanh toán lại.', $e);
        }
        $data = $response->json();
        if (!$response->successful() || !is_array($data) || (string)($data['code'] ?? '') !== '00') {
            // A 5xx/timeout may still have created a link. Never create a duplicate blindly.
            if ($response->serverError() || $response->status() === 429) {
                throw PaymentGatewayException::transport(self::PROVIDER, 'payOS chưa phản hồi chắc chắn; cần đối soát giao dịch.');
            }
            throw PaymentGatewayException::rejected(self::PROVIDER, 'payOS từ chối tạo liên kết thanh toán.', (string)($data['code'] ?? $response->status()));
        }
        $link = $data['data'] ?? [];
        if (!is_array($link) || (int)($link['orderCode'] ?? 0) !== $transactionId
            || (int)($link['amount'] ?? 0) !== $amount
            || !filter_var($link['checkoutUrl'] ?? '', FILTER_VALIDATE_URL)) {
            throw PaymentGatewayException::unknown(self::PROVIDER, 'Dữ liệu tạo thanh toán payOS không khớp đơn hàng.');
        }
        return new PaymentCreationResult(
            paymentUrl: $link['checkoutUrl'],
            providerOrderId: (string)$context['merchant_order_id'],
            providerRequestId: (string)$context['request_id'],
            responseCode: '00',
            message: 'Đã tạo liên kết thanh toán payOS.',
            rawResponse: ['paymentLinkId' => (string)($link['paymentLinkId'] ?? '')],
        );
    }

    /** Đối soát trực tiếp với payOS khi trình duyệt quay về (không tin query string). */
    public function lookupPaidTransaction(GiaoDichThanhToan $transaction): ?PaymentCallbackResult
    {
        if (! $this->isConfigured()) return null;
        $response = Http::withHeaders([
            'x-client-id' => $this->clientId(),
            'x-api-key' => $this->apiKey(),
        ])->acceptJson()->timeout($this->timeout())
            ->get($this->baseUrl().'/v2/payment-requests/'.(int) $transaction->getKey());
        if (! $response->successful() || (string) $response->json('code') !== '00') return null;
        $data = $response->json('data');
        if (! is_array($data)
            || (int) ($data['orderCode'] ?? 0) !== (int) $transaction->getKey()
            || (int) ($data['amount'] ?? -1) !== (int) round((float) $transaction->amount)
            || strtoupper((string) ($data['status'] ?? '')) !== 'PAID') return null;
        $transactions = $data['transactions'] ?? [];
        $reference = null;
        if (is_array($transactions)) {
            foreach ($transactions as $item) {
                if (is_array($item) && ! empty($item['reference'])) {
                    $reference = (string) $item['reference'];
                    break;
                }
            }
        }
        $reference ??= (string) ($data['paymentLinkId'] ?? '');
        if ($reference === '') return null;
        return new PaymentCallbackResult(
            true, true, false, (string) $transaction->merchant_transaction_id,
            null, $reference, (int) $data['amount'], '00', 'payOS đã xác nhận thanh toán qua đối soát.',
        );
    }

    public function verifyCallback(array $payload): PaymentCallbackResult
    {
        $data = $payload['data'] ?? null;
        $signature = (string) ($payload['signature'] ?? '');
        $valid = $this->isConfigured() && is_array($data) && $signature !== ''
            && hash_equals($this->signWebhookData($data), strtolower($signature));
        if (!$valid) {
            return new PaymentCallbackResult(false, false, false, null, null, null, null, null, 'Invalid payOS signature');
        }
        $id = filter_var($data['orderCode'] ?? null, FILTER_VALIDATE_INT);
        $transaction = $id ? GiaoDichThanhToan::query()->whereKey($id)->where('provider', self::PROVIDER)->first() : null;
        $isPaid = ($payload['success'] ?? false) === true
            && (string)($payload['code'] ?? '') === '00'
            && (string)($data['code'] ?? '') === '00';
        return new PaymentCallbackResult(
            validSignature: true,
            successful: $isPaid,
            pending: ! $isPaid,
            merchantOrderId: $transaction?->merchant_transaction_id,
            providerRequestId: null, // payOS webhook does not return our UUID.
            providerTransactionId: $isPaid ? (string)($data['reference'] ?? $data['paymentLinkId'] ?? '') : null,
            amount: isset($data['amount']) ? (int)$data['amount'] : null,
            responseCode: (string)($payload['code'] ?? ''),
            message: $isPaid ? 'payOS xác nhận đã thanh toán.' : 'payOS chưa xác nhận thanh toán.',
        );
    }

    /** payOS signs alphabetically sorted webhook data, not the outer envelope. */
    public function signWebhookData(array $data): string
    {
        ksort($data, SORT_STRING);
        $fields = [];
        foreach ($data as $key => $value) {
            if ($value === null || $value === 'null' || $value === 'undefined') $value = '';
            elseif (is_array($value)) {
                $value = array_map(function ($row) {
                    if (is_array($row)) ksort($row, SORT_STRING);
                    return $row;
                }, $value);
                $value = json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            } elseif (is_bool($value)) $value = $value ? 'true' : 'false';
            $fields[] = $key.'='.$value;
        }
        return hash_hmac('sha256', implode('&', $fields), $this->checksumKey());
    }

    private function clientId(): string { return trim((string)config('services.payos.client_id', '')); }
    private function apiKey(): string { return trim((string)config('services.payos.api_key', '')); }
    private function checksumKey(): string { return trim((string)config('services.payos.checksum_key', '')); }
    private function baseUrl(): string { return rtrim((string)config('services.payos.base_url', 'https://api-merchant.payos.vn'), '/'); }
    private function timeout(): int { return max(5, (int)config('services.payos.timeout', 20)); }
}
