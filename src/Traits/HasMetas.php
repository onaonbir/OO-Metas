<?php

declare(strict_types=1);

namespace OnaOnbir\OOMetas\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use OnaOnbir\OOMetas\Contracts\MetaServiceInterface;
use OnaOnbir\OOMetas\Models\Meta;

trait HasMetas
{
    protected function getMetaService(): MetaServiceInterface
    {
        return app(MetaServiceInterface::class);
    }

    // Basic operations
    public function getMeta(string $key, mixed $default = null, Model|string|null $connected = null): mixed
    {
        return $this->getMetaService()->get($this, $key, $default, $connected);
    }

    public function setMeta(string $key, mixed $value, ?Model $connected = null): void
    {
        $this->getMetaService()->set($this, $key, $value, $connected);
    }

    public function forgetMeta(string $key, ?Model $connected = null): void
    {
        $this->getMetaService()->forget($this, $key, $connected);
    }

    public function hasMeta(string $key, Model|string|null $connected = null): bool
    {
        return $this->getMetaService()->has($this, $key, $connected);
    }

    // Batch operations
    public function getManyMetas(array $keys, Model|string|null $connected = null): array
    {
        return $this->getMetaService()->getMany($this, $keys, $connected);
    }

    public function setManyMetas(array $values, ?Model $connected = null): void
    {
        $this->getMetaService()->setMany($this, $values, $connected);
    }

    public function forgetManyMetas(array $keys, ?Model $connected = null): void
    {
        $this->getMetaService()->forgetMany($this, $keys, $connected);
    }

    public function getAllMetas(?Model $connected = null): array
    {
        return $this->getMetaService()->getAll($this, $connected);
    }

    public function forgetAllMetas(?Model $connected = null): void
    {
        $this->getMetaService()->forgetAll($this, $connected);
    }

    // Numeric operations
    public function incrementMeta(string $key, int $value = 1, ?Model $connected = null): int
    {
        return $this->getMetaService()->increment($this, $key, $value, $connected);
    }

    public function decrementMeta(string $key, int $value = 1, ?Model $connected = null): int
    {
        return $this->getMetaService()->decrement($this, $key, $value, $connected);
    }

    // Special operations
    public function pullMeta(string $key, mixed $default = null, ?Model $connected = null): mixed
    {
        return $this->getMetaService()->pull($this, $key, $default, $connected);
    }

    public function rememberMeta(string $key, callable $callback, ?Model $connected = null): mixed
    {
        return $this->getMetaService()->remember($this, $key, $callback, $connected);
    }

    public function toggleMeta(string $key, ?Model $connected = null): bool
    {
        return $this->getMetaService()->toggle($this, $key, $connected);
    }

    // Array operations
    public function appendToMeta(string $key, mixed $value, ?Model $connected = null): array
    {
        return $this->getMetaService()->append($this, $key, $value, $connected);
    }

    public function prependToMeta(string $key, mixed $value, ?Model $connected = null): array
    {
        return $this->getMetaService()->prepend($this, $key, $value, $connected);
    }

    public function removeFromMetaArray(string $key, mixed $value, ?Model $connected = null): array
    {
        return $this->getMetaService()->removeFromArray($this, $key, $value, $connected);
    }

    // Eloquent relationship
    public function metas(): MorphMany
    {
        return $this->morphMany(Meta::class, 'model');
    }

    // Helper methods for common patterns
    public function getMetaAsArray(string $key, ?Model $connected = null): array
    {
        $value = $this->getMeta($key, [], $connected);

        return is_array($value) ? $value : [];
    }

    public function getMetaAsString(string $key, string $default = '', ?Model $connected = null): string
    {
        $value = $this->getMeta($key, $default, $connected);

        return is_string($value) ? $value : (string) $value;
    }

    public function getMetaAsInt(string $key, int $default = 0, ?Model $connected = null): int
    {
        $value = $this->getMeta($key, $default, $connected);

        return is_int($value) ? $value : (int) $value;
    }

    public function getMetaAsBool(string $key, bool $default = false, ?Model $connected = null): bool
    {
        $value = $this->getMeta($key, $default, $connected);

        return is_bool($value) ? $value : (bool) $value;
    }

    public function getMetaAsFloat(string $key, float $default = 0.0, ?Model $connected = null): float
    {
        $value = $this->getMeta($key, $default, $connected);

        return is_float($value) ? $value : (float) $value;
    }
}
