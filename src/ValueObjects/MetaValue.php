<?php

declare(strict_types=1);

namespace OnaOnbir\OOMetas\ValueObjects;

use JsonSerializable;

final readonly class MetaValue implements JsonSerializable
{
    public function __construct(public mixed $value)
    {
    }

    public static function make(mixed $value): self
    {
        return new self($value);
    }

    public function get(): mixed
    {
        return $this->value;
    }

    public function isNull(): bool
    {
        return $this->value === null;
    }

    public function isEmpty(): bool
    {
        if ($this->isNull()) {
            return true;
        }

        if (is_string($this->value)) {
            return trim($this->value) === '';
        }

        if (is_array($this->value)) {
            return empty($this->value);
        }

        if (is_countable($this->value)) {
            return count($this->value) === 0;
        }

        return false;
    }

    public function isArray(): bool
    {
        return is_array($this->value);
    }

    public function isString(): bool
    {
        return is_string($this->value);
    }

    public function isNumeric(): bool
    {
        return is_numeric($this->value);
    }

    public function isInteger(): bool
    {
        return is_int($this->value);
    }

    public function isFloat(): bool
    {
        return is_float($this->value);
    }

    public function isBool(): bool
    {
        return is_bool($this->value);
    }

    public function isObject(): bool
    {
        return is_object($this->value);
    }

    public function asString(): string
    {
        if ($this->isNull()) {
            return '';
        }

        if ($this->isString()) {
            return $this->value;
        }

        if ($this->isArray() || $this->isObject()) {
            return json_encode($this->value, JSON_UNESCAPED_UNICODE);
        }

        return (string) $this->value;
    }

    public function asArray(): array
    {
        if ($this->isNull()) {
            return [];
        }

        if ($this->isArray()) {
            return $this->value;
        }

        return [$this->value];
    }

    public function asInt(): int
    {
        return (int) $this->value;
    }

    public function asFloat(): float
    {
        return (float) $this->value;
    }

    public function asBool(): bool
    {
        return (bool) $this->value;
    }

    public function jsonSerialize(): mixed
    {
        return $this->value;
    }

    public function toString(): string
    {
        return $this->asString();
    }

    public function __toString(): string
    {
        return $this->toString();
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }
}
