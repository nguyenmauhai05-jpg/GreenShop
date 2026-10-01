<?php

namespace App\Services\Payments\DTO;

final readonly class PaymentCallbackResult
{
    public function __construct(
        public bool $validSignature,
        public bool $successful,
        public bool $pending,
        public ?string $merchantOrderId,
        public ?string $providerRequestId,
        public ?string $providerTransactionId,
        public ?int $amount,
        public int|string|null $responseCode,
        public string $message,
    ) {}
}
