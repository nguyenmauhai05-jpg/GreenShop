<?php

namespace App\Services\Payments\Contracts;

use App\Services\Payments\DTO\PaymentCallbackResult;
use App\Services\Payments\DTO\PaymentCreationResult;

interface PaymentGateway
{
    public function provider(): string;

    public function isConfigured(): bool;

    /**
     * @param  array{
     *     merchant_order_id: string,
     *     request_id: string,
     *     amount: int,
     *     order_info: string,
     *     return_url: string,
     *     callback_url: string,
     *     user_id?: int|string|null,
     *     items?: array<int, array<string, mixed>>
     * }  $context
     */
    public function createPayment(array $context): PaymentCreationResult;

    /**
     * Verify the provider signature and normalize callback data.
     *
     * This method never treats a browser return URL as proof of payment. Callers
     * should pass only server-to-server IPN/callback payloads here.
     *
     * @param  array<string, mixed>  $payload
     */
    public function verifyCallback(array $payload): PaymentCallbackResult;
}
