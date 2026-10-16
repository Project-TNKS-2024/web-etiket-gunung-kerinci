<?php

namespace App\Exceptions;

use RuntimeException;

/**
 * Kesalahan domain checkpoint yang membawa kode mesin (error code) agar mobile
 * dapat memutuskan rekonsiliasi tanpa mengandalkan pesan teks.
 */
class CheckpointException extends RuntimeException
{
    /**
     * @param string $errorCode  Kode mesin, mis. CHECKPOINT_ALREADY_RECORDED.
     * @param string $message    Pesan yang bisa dibaca manusia.
     * @param array  $context    Data tambahan (mis. record canonical saat 409).
     * @param int    $httpStatus Status HTTP yang sesuai.
     */
    public function __construct(
        public readonly string $errorCode,
        string $message,
        public readonly array $context = [],
        public readonly int $httpStatus = 409,
    ) {
        parent::__construct($message);
    }

    public function toErrors(): array
    {
        return ['code' => $this->errorCode] + $this->context;
    }
}
