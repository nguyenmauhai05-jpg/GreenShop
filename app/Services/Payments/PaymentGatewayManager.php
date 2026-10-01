<?php

namespace App\Services\Payments;

use App\Services\Payments\Contracts\PaymentGateway;
use App\Services\Payments\Exceptions\PaymentGatewayException;
use App\Services\Payments\Providers\ZaloPayPaymentGateway;
use App\Services\Payments\Providers\PayPalPaymentGateway;
use App\Services\Payments\Providers\PayOSPaymentGateway;

final class PaymentGatewayManager
{
    /** @var array<string, PaymentGateway> */
    private array $gateways;

    public function __construct(
        ZaloPayPaymentGateway $zaloPay,
        PayPalPaymentGateway $paypal,
        PayOSPaymentGateway $payos,
    ) {
        $this->gateways = [
            $zaloPay->provider() => $zaloPay,
            $paypal->provider() => $paypal,
            $payos->provider() => $payos,
        ];
    }

    public function resolve(string $provider): PaymentGateway
    {
        $provider = strtoupper(trim($provider));

        if (! isset($this->gateways[$provider])) {
            throw PaymentGatewayException::invalidRequest(
                $provider !== '' ? $provider : 'PAYMENT',
                "Cổng thanh toán [{$provider}] không được hỗ trợ.",
            );
        }

        return $this->gateways[$provider];
    }

    /**
     * @return list<string>
     */
    public function supportedProviders(): array
    {
        return array_keys($this->gateways);
    }
}
