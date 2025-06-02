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

    public function forgetMeta(string $key): void
    {
        OOMetas::forget($this, $key);
    }

    public function metas(): MorphMany
    {
        return $this->morphMany(Meta::class, 'model');
    }
}
