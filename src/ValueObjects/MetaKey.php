<?php

declare(strict_types=1);

namespace OnaOnbir\OOMetas\ValueObjects;

use OnaOnbir\OOMetas\Exceptions\InvalidMetaKeyException;

final readonly class MetaKey
{
    public string $mainKey;
    public ?string $nestedKey;

    public function __construct(public string $key)
    {
        $this->validateKey($key);
        [$this->mainKey, $this->nestedKey] = $this->splitKey($key);
    }

    public static function make(string $key): self
    {
        return new self($key);
    }

    public function isNested(): bool
    {
        return $this->nestedKey !== null;
    }

    public function getMainKey(): string
    {
        return $this->mainKey;
    }

    public function getNestedKey(): ?string
    {
        return $this->nestedKey;
    }

    public function getFullKey(): string
    {
        return $this->key;
    }

    public function toString(): string
    {
        return $this->key;
    }

    public function __toString(): string
    {
        return $this->toString();
    }

    private function validateKey(string $key): void
    {
        if (empty(trim($key))) {
            throw new InvalidMetaKeyException('Meta key cannot be empty');
        }

        if (strlen($key) > 255) {
            throw new InvalidMetaKeyException('Meta key cannot be longer than 255 characters');
        }

        // Check for invalid characters (optional, depending on your needs)
        if (preg_match('/[^\w\-\.]/', $key)) {
            throw new InvalidMetaKeyException('Meta key contains invalid characters. Only alphanumeric, dash, underscore and dot are allowed');
        }
    }

    private function splitKey(string $key): array
    {
        return str_contains($key, '.')
            ? explode('.', $key, 2)
            : [$key, null];
    }
}
