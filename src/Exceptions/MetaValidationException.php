<?php

declare(strict_types=1);

namespace OnaOnbir\OOMetas\Exceptions;

class MetaValidationException extends OOMetasException
{
    public function __construct(string $message = 'Meta validation failed', int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
