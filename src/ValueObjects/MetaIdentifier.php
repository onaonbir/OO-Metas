<?php

declare(strict_types=1);

namespace OnaOnbir\OOMetas\ValueObjects;

use Illuminate\Database\Eloquent\Model;

final readonly class MetaIdentifier
{
    public function __construct(
        public string $modelType,
        public string $modelId,
        public ?string $connectedType = null,
        public ?string $connectedId = null
    ) {}

    public static function fromModel(Model $model, ?Model $connected = null): self
    {
        return new self(
            modelType: get_class($model),
            modelId: (string) $model->getKey(),
            connectedType: $connected ? get_class($connected) : null,
            connectedId: $connected ? (string) $connected->getKey() : null
        );
    }

    public static function fromModelWithType(Model $model, ?string $connectedType = null): self
    {
        return new self(
            modelType: get_class($model),
            modelId: (string) $model->getKey(),
            connectedType: $connectedType,
            connectedId: null
        );
    }

    public function hasConnection(): bool
    {
        return $this->connectedType !== null || $this->connectedId !== null;
    }

    public function hasFullConnection(): bool
    {
        return $this->connectedType !== null && $this->connectedId !== null;
    }

    public function hasTypeOnlyConnection(): bool
    {
        return $this->connectedType !== null && $this->connectedId === null;
    }

    public function toArray(): array
    {
        return [
            'model_type' => $this->modelType,
            'model_id' => $this->modelId,
            'connected_type' => $this->connectedType,
            'connected_id' => $this->connectedId,
        ];
    }

    public function getModelSignature(): string
    {
        return "{$this->modelType}:{$this->modelId}";
    }

    public function getConnectionSignature(): ?string
    {
        if (! $this->hasConnection()) {
            return null;
        }

        if ($this->hasFullConnection()) {
            return "{$this->connectedType}:{$this->connectedId}";
        }

        return $this->connectedType;
    }

    public function getFullSignature(): string
    {
        $signature = $this->getModelSignature();

        if ($connection = $this->getConnectionSignature()) {
            $signature .= "@{$connection}";
        }

        return $signature;
    }

    public function toString(): string
    {
        return $this->getFullSignature();
    }

    public function __toString(): string
    {
        return $this->toString();
    }

    public function equals(self $other): bool
    {
        return $this->modelType === $other->modelType
            && $this->modelId === $other->modelId
            && $this->connectedType === $other->connectedType
            && $this->connectedId === $other->connectedId;
    }
}
