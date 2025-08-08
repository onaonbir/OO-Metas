<?php

declare(strict_types=1);

namespace OnaOnbir\OOMetas\Exceptions;

class MetaNotFoundException extends OOMetasException
{
    public function __construct(string $message = 'Meta not found', int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
