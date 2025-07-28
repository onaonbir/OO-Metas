<?php

namespace OnaOnbir\OOMetas\Traits;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use OnaOnbir\OOMetas\Models\Meta;
use OnaOnbir\OOMetas\OOMetas;

trait HasMetas
{
    public function getMeta(string $key, mixed $default = null, object|string|null $connected = null): mixed
    {
        return OOMetas::get($this, $key, $default, $connected);
    }

    public function setMeta(string $key, mixed $value, ?object $connected = null): void
    {
        OOMetas::set($this, $key, $value, $connected);
    }

    public function forgetMeta(string $key, ?object $connected = null): void
    {
        OOMetas::forget($this, $key, $connected);
    }

    public function hasMeta(string $key, object|string|null $connected = null): bool
    {
        return OOMetas::has($this, $key, $connected);
    }

    public function incrementMeta(string $key, int $value = 1, ?object $connected = null): int
    {
        return OOMetas::increment($this, $key, $value, $connected);
    }

    public function decrementMeta(string $key, int $value = 1, ?object $connected = null): int
    {
        return OOMetas::decrement($this, $key, $value, $connected);
    }

    public function pullMeta(string $key, mixed $default = null, ?object $connected = null): mixed
    {
        return OOMetas::pull($this, $key, $default, $connected);
    }

    public function rememberMeta(string $key, callable $callback, ?object $connected = null): mixed
    {
        return OOMetas::remember($this, $key, $callback, $connected);
    }

    public function metas(): MorphMany
    {
        return $this->morphMany(Meta::class, 'model');
    }
}
