<?php

namespace Tests\Feature\Payments;

use App\Services\Payments\Providers\MomoPaymentGateway;
use App\Services\Payments\Providers\ZaloPayPaymentGateway;
use Tests\TestCase;

class PaymentCallbackVerificationTest extends TestCase
{
    private const MOMO_PARTNER_CODE = 'MOMO_TEST_PARTNER';

    private const MOMO_ACCESS_KEY = 'momo-test-access-key';

    private const MOMO_SECRET_KEY = 'momo-test-secret-key';

    private const ZALOPAY_APP_ID = 2554;

    private const ZALOPAY_KEY_2 = 'zalopay-test-key-2';

    protected function setUp(): void
    {
        parent::setUp();

        config()->set([
            'services.momo.partner_code' => self::MOMO_PARTNER_CODE,
            'services.momo.access_key' => self::MOMO_ACCESS_KEY,
            'services.momo.secret_key' => self::MOMO_SECRET_KEY,
            'services.momo.base_url' => 'https://test-payment.momo.vn',
            'services.momo.create_endpoint' => '/v2/gateway/api/create',
            'services.zalopay.app_id' => self::ZALOPAY_APP_ID,
            'services.zalopay.key1' => 'zalopay-test-key-1',
            'services.zalopay.key2' => self::ZALOPAY_KEY_2,
            'services.zalopay.base_url' => 'https://sb-openapi.zalopay.vn',
            'services.zalopay.create_endpoint' => '/v2/create',
        ]);
    }

    public function test_momo_accepts_a_correctly_signed_success_ipn(): void
    {
        $payload = $this->signedMomoPayload();

        $result = (new MomoPaymentGateway)->verifyCallback($payload);

        $this->assertTrue($result->validSignature);
        $this->assertTrue($result->successful);
        $this->assertFalse($result->pending);
        $this->assertSame('GS-20260814-000001', $result->merchantOrderId);
        $this->assertSame('REQ-20260814-000001', $result->providerRequestId);
        $this->assertSame('4019912345', $result->providerTransactionId);
        $this->assertSame(125_000, $result->amount);
    }

    public function test_momo_rejects_payload_tampering_after_signature_was_created(): void
    {
        $payload = $this->signedMomoPayload();
        $payload['amount'] = 1;

        $result = (new MomoPaymentGateway)->verifyCallback($payload);

        $this->assertFalse($result->validSignature);
        $this->assertFalse($result->successful);
    }

    public function test_zalopay_accepts_a_correctly_signed_order_callback(): void
    {
        $payload = $this->signedZaloPayPayload();

        $result = (new ZaloPayPaymentGateway)->verifyCallback($payload);

        $this->assertTrue($result->validSignature);
        $this->assertTrue($result->successful);
        $this->assertFalse($result->pending);
        $this->assertSame('260814_GS-000001', $result->merchantOrderId);
        $this->assertSame('REQ-20260814-000001', $result->providerRequestId);
        $this->assertSame('240814000000001', $result->providerTransactionId);
        $this->assertSame(125_000, $result->amount);
    }

    public function test_zalopay_rejects_data_tampering_after_mac_was_created(): void
    {
        $payload = $this->signedZaloPayPayload();
        $tamperedData = json_decode($payload['data'], true, 512, JSON_THROW_ON_ERROR);
        $tamperedData['amount'] = 1;
        $payload['data'] = json_encode(
            $tamperedData,
            JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE,
        );

        $result = (new ZaloPayPaymentGateway)->verifyCallback($payload);

        $this->assertFalse($result->validSignature);
        $this->assertFalse($result->successful);
    }

    /** @return array<string, int|string> */
    private function signedMomoPayload(): array
    {
        $payload = [
            'amount' => 125_000,
            'extraData' => '',
            'message' => 'Successful.',
            'orderId' => 'GS-20260814-000001',
            'orderInfo' => 'Thanh toan don hang GreenShop',
            'orderType' => 'momo_wallet',
            'partnerCode' => self::MOMO_PARTNER_CODE,
            'payType' => 'qr',
            'requestId' => 'REQ-20260814-000001',
            'responseTime' => 1_765_700_000_000,
            'resultCode' => 0,
            'transId' => 4_019_912_345,
        ];

        $rawSignature = implode('&', [
            'accessKey='.self::MOMO_ACCESS_KEY,
            'amount='.$payload['amount'],
            'extraData='.$payload['extraData'],
            'message='.$payload['message'],
            'orderId='.$payload['orderId'],
            'orderInfo='.$payload['orderInfo'],
            'orderType='.$payload['orderType'],
            'partnerCode='.$payload['partnerCode'],
            'payType='.$payload['payType'],
            'requestId='.$payload['requestId'],
            'responseTime='.$payload['responseTime'],
            'resultCode='.$payload['resultCode'],
            'transId='.$payload['transId'],
        ]);

        $payload['signature'] = hash_hmac('sha256', $rawSignature, self::MOMO_SECRET_KEY);

        return $payload;
    }

    /** @return array{data: string, mac: string, type: int} */
    private function signedZaloPayPayload(): array
    {
        $data = json_encode([
            'app_id' => self::ZALOPAY_APP_ID,
            'app_trans_id' => '260814_GS-000001',
            'app_user' => '42',
            'app_time' => 1_765_700_000_000,
            'amount' => 125_000,
            'embed_data' => json_encode([
                'request_id' => 'REQ-20260814-000001',
            ], JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES),
            'item' => '[]',
            'zp_trans_id' => 240_814_000_000_001,
            'server_time' => 1_765_700_001_000,
            'channel' => 38,
            'merchant_user_id' => '42',
        ], JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        return [
            'data' => $data,
            'mac' => hash_hmac('sha256', $data, self::ZALOPAY_KEY_2),
            'type' => 1,
        ];
    }
}
