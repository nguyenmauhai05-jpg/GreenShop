<?php

namespace App\Services\Payments\Exceptions;

use RuntimeException;
use Throwable;

final class PaymentGatewayException extends RuntimeException
{
    public const CONFIGURATION = 'configuration';

    public const INVALID_REQUEST = 'invalid_request';

    public const REJECTED = 'rejected';

    public const TRANSPORT = 'transport';

    public const UNKNOWN = 'unknown';

    /**
     * @param  array<string, int|string|bool|null>  $safeContext
     */
    private function __construct(
        string $message,
        public readonly string $provider,
        public readonly string $type,
        public readonly int|string|null $responseCode = null,
        public readonly array $safeContext = [],
        ?Throwable $previous = null,
    ) {
        parent::__construct($message, is_int($responseCode) ? $responseCode : 0, $previous);
    }

    public static function configuration(string $provider, string $message): self
    {
        return new self($message, $provider, self::CONFIGURATION);
    }

    public static function invalidRequest(string $provider, string $message): self
    {
        return new self($message, $provider, self::INVALID_REQUEST);
    }

    /**
     * @param  array<string, int|string|bool|null>  $safeContext
     */
    public static function rejected(
        string $provider,
        string $message,
        int|string|null $responseCode = null,
        array $safeContext = [],
    ): self {
        return new self($message, $provider, self::REJECTED, $responseCode, $safeContext);
    }

    /**
     * @param  array<string, int|string|bool|null>  $safeContext
     */
    public static function transport(
        string $provider,
        string $message,
        ?Throwable $previous = null,
        array $safeContext = [],
    ): self {
        return new self($message, $provider, self::TRANSPORT, null, $safeContext, $previous);
    }

    /**
     * @param  array<string, int|string|bool|null>  $safeContext
     */
    public static function unknown(
        string $provider,
        string $message,
        ?Throwable $previous = null,
        array $safeContext = [],
    ): self {
        return new self($message, $provider, self::UNKNOWN, null, $safeContext, $previous);
    }
}
