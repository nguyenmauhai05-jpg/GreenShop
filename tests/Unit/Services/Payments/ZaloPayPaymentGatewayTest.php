<?php

namespace Tests\Unit\Services\Payments;

use App\Services\Payments\Exceptions\PaymentGatewayException;
use App\Services\Payments\Providers\ZaloPayPaymentGateway;
use Carbon\CarbonImmutable;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ZaloPayPaymentGatewayTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        CarbonImmutable::setTestNow(CarbonImmutable::parse(
            '2026-08-14 09:10:11',
            'Asia/Ho_Chi_Minh',
        ));

        config()->set('services.zalopay', [
            'app_id' => 2554,
            'key1' => 'zalopay-test-key-one',
            'key2' => 'zalopay-test-key-two',
            'base_url' => 'https://sb-openapi.zalopay.vn',
            'create_endpoint' => '/v2/create',
            'timeout' => 30,
            'expire_duration_seconds' => 900,
        ]);
    }

    protected function tearDown(): void
    {
        CarbonImmutable::setTestNow();
        parent::tearDown();
    }

    public function test_it_preserves_a_prefixed_app_trans_id_and_signs_create_order(): void
    {
        Http::fake([
            'sb-openapi.zalopay.vn/*' => Http::response([
                'return_code' => 1,
                'return_message' => 'Giao dịch thành công',
                'sub_return_code' => 1,
                'sub_return_message' => 'Success',
                'zp_trans_token' => 'sandbox-token',
                'order_url' => 'https://sbgateway.zalopay.vn/openinapp?token=sandbox-token',
            ]),
        ]);

        $context = $this->context('260814_ORDER_123');
        $result = (new ZaloPayPaymentGateway)->createPayment($context);

        Http::assertSent(function (Request $request): bool {
            $data = $request->data();
            $expectedItems = json_encode(
                [['itemid' => 'PLANT_1', 'itemname' => 'Monstera', 'itemprice' => 125000, 'itemquantity' => 1]],
                JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE,
            );
            $expectedEmbedData = json_encode([
                'redirecturl' => 'https://shop.test/payments/zalopay/return',
                'preferred_payment_method' => ['zalopay_wallet'],
                'request_id' => 'REQ_ZALO_123',
            ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            $macInput = implode('|', [
                2554,
                '260814_ORDER_123',
                '42',
                125000,
                $data['app_time'],
                $expectedEmbedData,
                $expectedItems,
            ]);

            return $request->url() === 'https://sb-openapi.zalopay.vn/v2/create'
                && $request->method() === 'POST'
                && $data['app_trans_id'] === '260814_ORDER_123'
                && $data['expire_duration_seconds'] === 900
                && $data['embed_data'] === $expectedEmbedData
                && $data['item'] === $expectedItems
                && $data['mac'] === hash_hmac('sha256', $macInput, 'zalopay-test-key-one');
        });

        $this->assertSame('260814_ORDER_123', $result->providerOrderId);
        $this->assertSame('REQ_ZALO_123', $result->providerRequestId);
        $this->assertSame('https://sbgateway.zalopay.vn/openinapp?token=sandbox-token', $result->paymentUrl);
    }

    public function test_it_prefixes_an_unformatted_order_id_in_vietnam_time(): void
    {
        Http::fake([
            'sb-openapi.zalopay.vn/*' => Http::response([
                'return_code' => 1,
                'return_message' => 'Success',
                'order_url' => 'https://sbgateway.zalopay.vn/openinapp?token=sandbox-token',
            ]),
        ]);

        $result = (new ZaloPayPaymentGateway)->createPayment($this->context('ORDER_456'));

        $this->assertSame('260814_ORDER_456', $result->providerOrderId);
        Http::assertSent(fn (Request $request): bool => $request['app_trans_id'] === '260814_ORDER_456');
    }

    public function test_a_processing_create_response_is_retryable_and_has_no_empty_redirect(): void
    {
        Http::fake([
            'sb-openapi.zalopay.vn/*' => Http::response([
                'return_code' => 3,
                'return_message' => 'Processing',
            ]),
        ]);

        try {
            (new ZaloPayPaymentGateway)->createPayment($this->context('260814_ORDER_789'));
            $this->fail('Expected a PaymentGatewayException.');
        } catch (PaymentGatewayException $exception) {
            $this->assertSame(PaymentGatewayException::UNKNOWN, $exception->type);
            $this->assertSame('Processing', $exception->getMessage());
        }
    }

    public function test_a_duplicate_app_transaction_after_timeout_remains_pending_for_reconciliation(): void
    {
        Http::fake([
            'sb-openapi.zalopay.vn/*' => Http::response([
                'return_code' => 2,
                'return_message' => 'Giao dịch bị trùng',
                'sub_return_code' => -68,
                'sub_return_message' => 'DUPLICATE_APPS_TRANS_ID',
            ]),
        ]);

        try {
            (new ZaloPayPaymentGateway)->createPayment($this->context('260814_ORDER_DUPLICATE'));
            $this->fail('Expected an ambiguous duplicate to require reconciliation.');
        } catch (PaymentGatewayException $exception) {
            $this->assertSame(PaymentGatewayException::UNKNOWN, $exception->type);
            $this->assertSame(-68, $exception->safeContext['sub_return_code']);
        }
    }

    public function test_it_verifies_the_outer_data_mac_with_key_two(): void
    {
        $embedData = json_encode([
            'redirecturl' => 'https://shop.test/payments/zalopay/return',
            'preferred_payment_method' => ['zalopay_wallet'],
            'request_id' => 'REQ_ZALO_123',
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        $data = json_encode([
            'app_id' => 2554,
            'app_trans_id' => '260814_ORDER_123',
            'app_time' => 1_786_665_600_000,
            'app_user' => '42',
            'amount' => 125000,
            'embed_data' => $embedData,
            'item' => '[]',
            'zp_trans_id' => 2_608_140_000_065_750,
            'server_time' => 1_786_665_700_000,
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        $result = (new ZaloPayPaymentGateway)->verifyCallback([
            'data' => $data,
            'mac' => hash_hmac('sha256', $data, 'zalopay-test-key-two'),
            'type' => 1,
        ]);

        $this->assertTrue($result->validSignature);
        $this->assertTrue($result->successful);
        $this->assertFalse($result->pending);
        $this->assertSame('260814_ORDER_123', $result->merchantOrderId);
        $this->assertSame('REQ_ZALO_123', $result->providerRequestId);
        $this->assertSame('2608140000065750', $result->providerTransactionId);
        $this->assertSame(125000, $result->amount);
    }

    public function test_it_rejects_a_tampered_zalopay_callback(): void
    {
        $data = json_encode([
            'app_id' => 2554,
            'app_trans_id' => '260814_ORDER_123',
            'amount' => 125000,
            'zp_trans_id' => 2_608_140_000_065_750,
        ]);

        $result = (new ZaloPayPaymentGateway)->verifyCallback([
            'data' => $data,
            'mac' => str_repeat('0', 64),
            'type' => 1,
        ]);

        $this->assertFalse($result->validSignature);
        $this->assertFalse($result->successful);
    }

    public function test_a_valid_mac_for_another_app_is_not_an_accepted_signature(): void
    {
        $data = json_encode([
            'app_id' => 9999,
            'app_trans_id' => '260814_ORDER_123',
            'amount' => 125000,
            'zp_trans_id' => 2_608_140_000_065_750,
        ]);

        $result = (new ZaloPayPaymentGateway)->verifyCallback([
            'data' => $data,
            'mac' => hash_hmac('sha256', $data, 'zalopay-test-key-two'),
            'type' => 1,
        ]);

        $this->assertFalse($result->validSignature);
        $this->assertFalse($result->successful);
    }

    public function test_a_valid_mac_with_non_order_callback_type_is_not_accepted(): void
    {
        $data = json_encode([
            'app_id' => 2554,
            'app_trans_id' => '260814_ORDER_123',
            'amount' => 125000,
            'zp_trans_id' => 2_608_140_000_065_750,
        ]);

        $result = (new ZaloPayPaymentGateway)->verifyCallback([
            'data' => $data,
            'mac' => hash_hmac('sha256', $data, 'zalopay-test-key-two'),
            'type' => 2,
        ]);

        $this->assertFalse($result->validSignature);
        $this->assertFalse($result->successful);
    }

    /** @return array<string, mixed> */
    private function context(string $merchantOrderId): array
    {
        return [
            'merchant_order_id' => $merchantOrderId,
            'request_id' => 'REQ_ZALO_123',
            'amount' => 125000,
            'order_info' => 'GreenShop - Thanh toan don hang',
            'return_url' => 'https://shop.test/payments/zalopay/return',
            'callback_url' => 'https://shop.test/payments/zalopay/callback',
            'user_id' => 42,
            'items' => [
                [
                    'itemid' => 'PLANT_1',
                    'itemname' => 'Monstera',
                    'itemprice' => 125000,
                    'itemquantity' => 1,
                ],
            ],
        ];
    }
}
