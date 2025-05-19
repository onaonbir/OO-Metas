<?php

namespace OnaOnbir\OOMetas\Traits;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use OnaOnbir\OOMetas\Models\Meta;
use OnaOnbir\OOMetas\OOMetas;

trait HasMetas
{
    public function __get($key)
    {
        if ($this->hasMetaKey($key)) {
            return $this->getMeta($key);
        }

        return parent::__get($key);
    }

    public function __set($key, $value)
    {
        if ($this->hasMetaKey($key)) {
            $this->setMeta($key, $value);

            return;
        }

        parent::__set($key, $value);
    }

    protected function hasMetaKey($key): bool
    {
        if (method_exists($this, 'metaCasts')) {
            return array_key_exists($key, $this->metaCasts());
        }

        return false;
    }

    public function getMeta(string $key, mixed $default = null, ?object $connected = null): mixed
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

    public function metas(): MorphMany
    {
        return $this->morphMany(Meta::class, 'model');
    }
}
