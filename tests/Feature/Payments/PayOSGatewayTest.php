<?php

namespace Tests\Feature\Payments;

use App\Services\Payments\Providers\PayOSPaymentGateway;
use Tests\TestCase;

class PayOSGatewayTest extends TestCase
{
    public function test_rejects_unsigned_and_modified_webhooks(): void
    {
        config()->set('services.payos.client_id', 'example');
        config()->set('services.payos.api_key', 'example');
        config()->set('services.payos.checksum_key', 'test-key');
        $gateway = new PayOSPaymentGateway();
        $data = ['orderCode' => 1, 'amount' => 50000, 'reference' => 'TEST'];
        $signature = $gateway->signWebhookData($data);
        $this->assertFalse($gateway->verifyCallback([
            'success' => true, 'code' => '00', 'data' => [...$data, 'amount' => 1],
            'signature' => $signature,
        ])->validSignature);
        $this->assertFalse($gateway->verifyCallback([
            'success' => true, 'code' => '00', 'data' => $data,
        ])->validSignature);
    }

    public function test_requires_all_three_credentials(): void
    {
        config()->set('services.payos.client_id', 'example');
        config()->set('services.payos.api_key', 'example');
        config()->set('services.payos.checksum_key', '');
        $this->assertFalse((new PayOSPaymentGateway())->isConfigured());
    }
}
