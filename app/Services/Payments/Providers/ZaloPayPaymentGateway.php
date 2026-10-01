<?php

namespace App\Services\Payments\Providers;

use App\Services\Payments\Contracts\PaymentGateway;
use App\Services\Payments\DTO\PaymentCallbackResult;
use App\Services\Payments\DTO\PaymentCreationResult;
use App\Services\Payments\Exceptions\PaymentGatewayException;
use DateTimeZone;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use JsonException;

final class ZaloPayPaymentGateway implements PaymentGateway
{
    public const PROVIDER = 'ZALOPAY';

    private const VIETNAM_TIMEZONE = 'Asia/Ho_Chi_Minh';

    public function provider(): string
    {
        return self::PROVIDER;
    }

    public function isConfigured(): bool
    {
        return $this->appId() > 0
            && $this->configuredString('key1') !== ''
            && $this->configuredString('key2') !== ''
            && filter_var($this->endpoint(), FILTER_VALIDATE_URL) !== false;
    }

    public function createPayment(array $context): PaymentCreationResult
    {
        $this->ensureConfigured();
        $context = $this->validateContext($context);

        $createdAt = now(new DateTimeZone(self::VIETNAM_TIMEZONE));
        $appTransId = $this->appTransactionId(
            $context['merchant_order_id'],
            $createdAt->format('ymd'),
        );
        $appTime = $createdAt->getTimestampMs();
        $appUser = $context['user_id'] === null || (string) $context['user_id'] === ''
            ? 'greenshop'
            : (string) $context['user_id'];

        try {
            $items = json_encode(
                $context['items'],
                JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE,
            );
            $embedData = json_encode([
                'redirecturl' => $context['return_url'],
                'preferred_payment_method' => ['zalopay_wallet'],
                'request_id' => $context['request_id'],
            ], JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        } catch (JsonException $exception) {
            throw PaymentGatewayException::invalidRequest(
                self::PROVIDER,
                'Không thể mã hóa items hoặc embed_data cho ZaloPay.',
            );
        }

        $macInput = implode('|', [
            $this->appId(),
            $appTransId,
            $appUser,
            $context['amount'],
            $appTime,
            $embedData,
            $items,
        ]);

        $payload = [
            'app_id' => $this->appId(),
            'app_user' => $appUser,
            'app_trans_id' => $appTransId,
            'app_time' => $appTime,
            'amount' => $context['amount'],
            'description' => $context['order_info'],
            'callback_url' => $context['callback_url'],
            'expire_duration_seconds' => $this->expireDurationSeconds(),
            'item' => $items,
            'embed_data' => $embedData,
            'bank_code' => '',
            'mac' => hash_hmac('sha256', $macInput, $this->configuredString('key1')),
        ];

        try {
            $response = Http::asJson()
                ->acceptJson()
                ->connectTimeout(min(10, $this->timeout()))
                ->timeout($this->timeout())
                ->post($this->endpoint(), $payload);
        } catch (ConnectionException $exception) {
            throw PaymentGatewayException::transport(
                self::PROVIDER,
                'Không thể kết nối đến cổng thanh toán ZaloPay.',
                $exception,
                ['endpoint' => $this->endpoint()],
            );
        } catch (\Throwable $exception) {
            throw PaymentGatewayException::unknown(
                self::PROVIDER,
                'Có lỗi không xác định khi tạo giao dịch ZaloPay.',
                $exception,
            );
        }

        $data = $response->json();
        if (! is_array($data)) {
            if (! $response->successful()) {
                throw PaymentGatewayException::transport(
                    self::PROVIDER,
                    'ZaloPay trả về phản hồi HTTP không thành công.',
                    safeContext: ['http_status' => $response->status()],
                );
            }

            throw PaymentGatewayException::unknown(
                self::PROVIDER,
                'Phản hồi của ZaloPay không đúng định dạng JSON.',
                safeContext: ['http_status' => $response->status()],
            );
        }

        $returnCode = $this->normalizeResponseCode($data['return_code'] ?? null);
        $message = $this->stringValue($data['return_message'] ?? '')
            ?: $this->stringValue($data['sub_return_message'] ?? '')
            ?: 'ZaloPay không cung cấp thông báo.';

        if (! $response->successful()) {
            throw PaymentGatewayException::transport(
                self::PROVIDER,
                'ZaloPay trả về phản hồi HTTP không thành công.',
                safeContext: [
                    'http_status' => $response->status(),
                    'return_code' => $returnCode,
                ],
            );
        }

        if ($returnCode === null) {
            throw PaymentGatewayException::unknown(
                self::PROVIDER,
                'Phản hồi của ZaloPay thiếu return_code.',
                safeContext: ['http_status' => $response->status()],
            );
        }

        $subReturnCode = $this->normalizeResponseCode($data['sub_return_code'] ?? null);

        if ((int) $returnCode === 2 && (int) $subReturnCode === -68) {
            // Sau một timeout, app_trans_id trùng có thể là giao dịch trước đã
            // được ZaloPay nhận. Giữ pending để callback/đối soát xử lý, không
            // hủy đơn và hoàn kho như một lỗi từ chối chắc chắn.
            throw PaymentGatewayException::unknown(
                self::PROVIDER,
                $message,
                safeContext: [
                    'return_code' => $returnCode,
                    'sub_return_code' => $subReturnCode,
                ],
            );
        }

        if ((int) $returnCode === 2) {
            throw PaymentGatewayException::rejected(
                self::PROVIDER,
                $message,
                $returnCode,
                [
                    'http_status' => $response->status(),
                    'sub_return_code' => $subReturnCode,
                ],
            );
        }

        if ((int) $returnCode === 3) {
            throw PaymentGatewayException::unknown(
                self::PROVIDER,
                $message,
                safeContext: ['return_code' => $returnCode],
            );
        }

        if ((int) $returnCode !== 1) {
            throw PaymentGatewayException::unknown(
                self::PROVIDER,
                'ZaloPay trả về return_code không xác định.',
                safeContext: ['return_code' => $returnCode],
            );
        }

        $paymentUrl = $this->nullableString($data['order_url'] ?? null);
        if ((int) $returnCode === 1
            && ($paymentUrl === null || filter_var($paymentUrl, FILTER_VALIDATE_URL) === false)) {
            throw PaymentGatewayException::unknown(
                self::PROVIDER,
                'ZaloPay chấp nhận yêu cầu nhưng không trả về order_url hợp lệ.',
                safeContext: ['return_code' => $returnCode],
            );
        }

        return new PaymentCreationResult(
            paymentUrl: $paymentUrl,
            providerOrderId: $appTransId,
            providerRequestId: $context['request_id'],
            responseCode: $returnCode,
            message: $message,
            rawResponse: $data,
        );
    }

    public function verifyCallback(array $payload): PaymentCallbackResult
    {
        $this->ensureConfigured();

        $dataString = isset($payload['data']) && is_string($payload['data'])
            ? $payload['data']
            : null;
        $receivedMac = $this->nullableString($payload['mac'] ?? null);

        if ($dataString === null || $receivedMac === null) {
            return new PaymentCallbackResult(
                validSignature: false,
                successful: false,
                pending: false,
                merchantOrderId: null,
                providerRequestId: null,
                providerTransactionId: null,
                amount: null,
                responseCode: 0,
                message: 'ZaloPay callback thiếu data hoặc mac.',
            );
        }

        $expectedMac = hash_hmac('sha256', $dataString, $this->configuredString('key2'));
        $validMac = hash_equals($expectedMac, strtolower($receivedMac));

        try {
            $data = json_decode($dataString, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException) {
            return new PaymentCallbackResult(
                validSignature: false,
                successful: false,
                pending: false,
                merchantOrderId: null,
                providerRequestId: null,
                providerTransactionId: null,
                amount: null,
                responseCode: 0,
                message: 'data trong ZaloPay callback không phải JSON hợp lệ.',
            );
        }

        if (! is_array($data)) {
            return new PaymentCallbackResult(
                validSignature: false,
                successful: false,
                pending: false,
                merchantOrderId: null,
                providerRequestId: null,
                providerTransactionId: null,
                amount: null,
                responseCode: 0,
                message: 'data trong ZaloPay callback không đúng định dạng.',
            );
        }

        $merchantOrderId = $this->nullableString($data['app_trans_id'] ?? null);
        $providerTransactionId = $this->nullableString($data['zp_trans_id'] ?? null);
        $amount = $this->integerValue($data['amount'] ?? null);
        $providerRequestId = $this->requestIdFromEmbedData($data['embed_data'] ?? null);
        $orderCallback = (int) ($payload['type'] ?? 0) === 1;
        $matchingApp = $this->integerValue($data['app_id'] ?? null) === $this->appId();
        $completeData = $merchantOrderId !== null
            && $providerTransactionId !== null
            && $amount !== null;
        $validCallback = $validMac && $orderCallback && $matchingApp && $completeData;

        if (! $validMac) {
            $message = 'Chữ ký ZaloPay callback không hợp lệ.';
            $responseCode = -1;
        } elseif (! $orderCallback) {
            $message = 'Callback ZaloPay không phải callback thanh toán đơn hàng.';
            $responseCode = 0;
        } elseif (! $matchingApp) {
            $message = 'app_id trong ZaloPay callback không khớp cấu hình.';
            $responseCode = 0;
        } elseif (! $completeData) {
            $message = 'ZaloPay callback thiếu dữ liệu giao dịch bắt buộc.';
            $responseCode = 0;
        } else {
            // ZaloPay sends this order callback only after collecting payment.
            $message = 'Giao dịch ZaloPay thành công.';
            $responseCode = 1;
        }

        return new PaymentCallbackResult(
            validSignature: $validCallback,
            successful: $validCallback,
            pending: false,
            merchantOrderId: $merchantOrderId,
            providerRequestId: $providerRequestId,
            providerTransactionId: $providerTransactionId,
            amount: $amount,
            responseCode: $responseCode,
            message: $message,
        );
    }

    /**
     * @return array{
     *     merchant_order_id: string,
     *     request_id: string,
     *     amount: int,
     *     order_info: string,
     *     return_url: string,
     *     callback_url: string,
     *     user_id: int|string|null,
     *     items: array<int, array<string, mixed>>
     * }
     */
    private function validateContext(array $context): array
    {
        $merchantOrderId = $this->requiredString($context, 'merchant_order_id');
        $requestId = $this->requiredString($context, 'request_id');
        $orderInfo = $this->requiredString($context, 'order_info');
        $returnUrl = $this->requiredUrl($context, 'return_url');
        $callbackUrl = $this->requiredUrl($context, 'callback_url');

        if (! isset($context['amount']) || ! is_int($context['amount']) || $context['amount'] <= 0) {
            throw PaymentGatewayException::invalidRequest(
                self::PROVIDER,
                'amount phải là số nguyên VND lớn hơn 0.',
            );
        }

        if (strlen($orderInfo) > 256) {
            throw PaymentGatewayException::invalidRequest(
                self::PROVIDER,
                'order_info của ZaloPay không được dài quá 256 ký tự.',
            );
        }

        $items = $context['items'] ?? [];
        if (! is_array($items)) {
            throw PaymentGatewayException::invalidRequest(
                self::PROVIDER,
                'items phải là một mảng.',
            );
        }

        foreach ($items as $item) {
            if (! is_array($item)) {
                throw PaymentGatewayException::invalidRequest(
                    self::PROVIDER,
                    'Mỗi phần tử items phải là một mảng.',
                );
            }
        }

        $userId = $context['user_id'] ?? null;
        if ($userId !== null && ! is_int($userId) && ! is_string($userId)) {
            throw PaymentGatewayException::invalidRequest(
                self::PROVIDER,
                'user_id phải là chuỗi, số nguyên hoặc null.',
            );
        }

        $appUser = $userId === null || (string) $userId === '' ? 'greenshop' : (string) $userId;
        if (strlen($appUser) > 50) {
            throw PaymentGatewayException::invalidRequest(
                self::PROVIDER,
                'user_id dùng làm app_user không được dài quá 50 ký tự.',
            );
        }

        return [
            'merchant_order_id' => $merchantOrderId,
            'request_id' => $requestId,
            'amount' => $context['amount'],
            'order_info' => $orderInfo,
            'return_url' => $returnUrl,
            'callback_url' => $callbackUrl,
            'user_id' => $userId,
            'items' => array_values($items),
        ];
    }

    private function appTransactionId(string $merchantOrderId, string $datePrefix): string
    {
        $appTransId = preg_match('/^\d{6}_/', $merchantOrderId) === 1
            ? $merchantOrderId
            : $datePrefix.'_'.$merchantOrderId;

        if (strlen($appTransId) > 40) {
            throw PaymentGatewayException::invalidRequest(
                self::PROVIDER,
                'app_trans_id của ZaloPay không được dài quá 40 ký tự.',
            );
        }

        return $appTransId;
    }

    private function requestIdFromEmbedData(mixed $embedData): ?string
    {
        if (! is_string($embedData) || $embedData === '') {
            return null;
        }

        try {
            $decoded = json_decode($embedData, true, 512, JSON_THROW_ON_ERROR);

            return is_array($decoded)
                ? $this->nullableString($decoded['request_id'] ?? null)
                : null;
        } catch (JsonException) {
            return null;
        }
    }

    private function ensureConfigured(): void
    {
        if (! $this->isConfigured()) {
            throw PaymentGatewayException::configuration(
                self::PROVIDER,
                'ZaloPay chưa được cấu hình đầy đủ. Hãy kiểm tra app ID, key1, key2 và endpoint.',
            );
        }
    }

    private function endpoint(): string
    {
        return rtrim($this->configuredString('base_url'), '/')
            .'/'.ltrim($this->configuredString('create_endpoint'), '/');
    }

    private function appId(): int
    {
        return (int) config('services.zalopay.app_id', 0);
    }

    private function timeout(): int
    {
        return max(5, (int) config('services.zalopay.timeout', 30));
    }

    private function expireDurationSeconds(): int
    {
        return min(
            2_592_000,
            max(300, (int) config('services.zalopay.expire_duration_seconds', 900)),
        );
    }

    private function configuredString(string $key, string $default = ''): string
    {
        return trim((string) config("services.zalopay.{$key}", $default));
    }

    private function requiredString(array $context, string $key): string
    {
        $value = $context[$key] ?? null;
        if (! is_string($value) && ! is_int($value)) {
            throw PaymentGatewayException::invalidRequest(
                self::PROVIDER,
                "{$key} là trường bắt buộc.",
            );
        }

        $value = trim((string) $value);
        if ($value === '') {
            throw PaymentGatewayException::invalidRequest(
                self::PROVIDER,
                "{$key} không được để trống.",
            );
        }

        return $value;
    }

    private function requiredUrl(array $context, string $key): string
    {
        $url = $this->requiredString($context, $key);
        if (filter_var($url, FILTER_VALIDATE_URL) === false) {
            throw PaymentGatewayException::invalidRequest(
                self::PROVIDER,
                "{$key} phải là URL hợp lệ.",
            );
        }

        return $url;
    }

    private function nullableString(mixed $value): ?string
    {
        if (! is_scalar($value)) {
            return null;
        }

        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }

    private function stringValue(mixed $value): string
    {
        return is_scalar($value) ? (string) $value : '';
    }

    private function integerValue(mixed $value): ?int
    {
        return is_int($value) || (is_string($value) && ctype_digit($value))
            ? (int) $value
            : null;
    }

    private function normalizeResponseCode(mixed $value): int|string|null
    {
        if (is_int($value)) {
            return $value;
        }

        if (is_string($value) && $value !== '') {
            return preg_match('/^-?\d+$/D', $value) === 1 ? (int) $value : $value;
        }

        return null;
    }
}
