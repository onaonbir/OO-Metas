<?php

declare(strict_types=1);

namespace OnaOnbir\OOMetas\Exceptions;

class InvalidMetaKeyException extends OOMetasException
{
    public function __construct(string $message = 'Invalid meta key provided', int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
