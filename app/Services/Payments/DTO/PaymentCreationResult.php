<?php

namespace App\Services\Payments\DTO;

final readonly class PaymentCreationResult
{
    /**
     * @param  array<string, mixed>  $rawResponse
     */
    public function __construct(
        public ?string $paymentUrl,
        public string $providerOrderId,
        public string $providerRequestId,
        public int|string|null $responseCode,
        public string $message,
        public array $rawResponse,
    ) {}
}
