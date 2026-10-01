<?php

namespace Tests\Unit\Services\Payments;

use App\Services\Payments\Exceptions\PaymentGatewayException;
use App\Services\Payments\PaymentGatewayManager;
use App\Services\Payments\Providers\MomoPaymentGateway;
use App\Services\Payments\Providers\ZaloPayPaymentGateway;
use PHPUnit\Framework\TestCase;

class PaymentGatewayManagerTest extends TestCase
{
    public function test_it_resolves_supported_providers_case_insensitively(): void
    {
        $manager = new PaymentGatewayManager(
            new MomoPaymentGateway,
            new ZaloPayPaymentGateway,
        );

        $this->assertInstanceOf(MomoPaymentGateway::class, $manager->resolve('momo'));
        $this->assertInstanceOf(ZaloPayPaymentGateway::class, $manager->resolve(' ZaloPay '));
        $this->assertSame(['MOMO', 'ZALOPAY'], $manager->supportedProviders());
    }

    public function test_it_rejects_an_unknown_provider(): void
    {
        $manager = new PaymentGatewayManager(
            new MomoPaymentGateway,
            new ZaloPayPaymentGateway,
        );

        try {
            $manager->resolve('unknown');
            $this->fail('Expected a PaymentGatewayException.');
        } catch (PaymentGatewayException $exception) {
            $this->assertSame(PaymentGatewayException::INVALID_REQUEST, $exception->type);
            $this->assertSame('UNKNOWN', $exception->provider);
        }
    }
}
