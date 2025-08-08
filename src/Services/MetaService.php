<?php

declare(strict_types=1);

namespace OnaOnbir\OOMetas\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use OnaOnbir\OOMetas\Contracts\MetaRepositoryInterface;
use OnaOnbir\OOMetas\Contracts\MetaServiceInterface;
use OnaOnbir\OOMetas\ValueObjects\MetaIdentifier;
use OnaOnbir\OOMetas\ValueObjects\MetaKey;
use OnaOnbir\OOMetas\ValueObjects\MetaValue;

class MetaService implements MetaServiceInterface
{
    public function __construct(
        private MetaRepositoryInterface $repository
    ) {}

    public function get(Model $model, string $key, mixed $default = null, Model|string|null $connected = null): mixed
    {
        $identifier = $this->createIdentifier($model, $connected);
        $metaKey = MetaKey::make($key);
        
        $meta = $this->repository->find($identifier, $metaKey);
        
        if (!$meta) {
            return $default;
        }

        if ($metaKey->isNested()) {
            return data_get($meta->value, $metaKey->getNestedKey(), $default);
        }

        return $meta->value ?? $default;
    }

    public function getMany(Model $model, array $keys, Model|string|null $connected = null): array
    {
        $identifier = $this->createIdentifier($model, $connected);
        $metaKeys = array_map(fn($key) => MetaKey::make($key), $keys);
        
        $metas = $this->repository->findMany($identifier, $metaKeys);
        $result = [];

        foreach ($keys as $key) {
            $metaKey = MetaKey::make($key);
            $meta = $metas->firstWhere('key', $metaKey->getMainKey());
            
            if ($meta) {
                if ($metaKey->isNested()) {
                    $result[$key] = data_get($meta->value, $metaKey->getNestedKey());
                } else {
                    $result[$key] = $meta->value;
                }
            } else {
                $result[$key] = null;
            }
        }

        return $result;
    }

    public function getAll(Model $model, Model|string|null $connected = null): array
    {
        $identifier = $this->createIdentifier($model, $connected);
        $metas = $this->repository->findByIdentifier($identifier);
        
        $result = [];
        foreach ($metas as $meta) {
            $result[$meta->key] = $meta->value;
        }

        return $result;
    }

    public function set(Model $model, string $key, mixed $value, ?Model $connected = null): void
    {
        $identifier = $this->createIdentifier($model, $connected);
        $metaKey = MetaKey::make($key);
        
        $this->repository->save($identifier, $metaKey, $value);
    }

    public function setMany(Model $model, array $values, ?Model $connected = null): void
    {
        $identifier = $this->createIdentifier($model, $connected);
        $this->repository->saveMany($identifier, $values);
    }

    public function forget(Model $model, string $key, ?Model $connected = null): void
    {
        $identifier = $this->createIdentifier($model, $connected);
        $metaKey = MetaKey::make($key);
        
        $this->repository->delete($identifier, $metaKey);
    }

    public function forgetMany(Model $model, array $keys, ?Model $connected = null): void
    {
        $identifier = $this->createIdentifier($model, $connected);
        $metaKeys = array_map(fn($key) => MetaKey::make($key), $keys);
        
        $this->repository->deleteMany($identifier, $metaKeys);
    }

    public function forgetAll(Model $model, ?Model $connected = null): void
    {
        $identifier = $this->createIdentifier($model, $connected);
        $this->repository->deleteByIdentifier($identifier);
    }

    public function has(Model $model, string $key, Model|string|null $connected = null): bool
    {
        $identifier = $this->createIdentifier($model, $connected);
        $metaKey = MetaKey::make($key);
        
        return $this->repository->exists($identifier, $metaKey);
    }

    public function increment(Model $model, string $key, int $value = 1, ?Model $connected = null): int
    {
        $current = $this->get($model, $key, 0, $connected);
        $newValue = (int) $current + $value;
        $this->set($model, $key, $newValue, $connected);
        
        return $newValue;
    }

    public function decrement(Model $model, string $key, int $value = 1, ?Model $connected = null): int
    {
        return $this->increment($model, $key, -$value, $connected);
    }

    public function pull(Model $model, string $key, mixed $default = null, ?Model $connected = null): mixed
    {
        $value = $this->get($model, $key, $default, $connected);
        $this->forget($model, $key, $connected);
        
        return $value;
    }

    public function remember(Model $model, string $key, callable $callback, ?Model $connected = null): mixed
    {
        if ($this->has($model, $key, $connected)) {
            return $this->get($model, $key, null, $connected);
        }

        $value = $callback();
        $this->set($model, $key, $value, $connected);
        
        return $value;
    }

    public function toggle(Model $model, string $key, ?Model $connected = null): bool
    {
        $current = $this->get($model, $key, false, $connected);
        $newValue = !$current;
        $this->set($model, $key, $newValue, $connected);
        
        return $newValue;
    }

    public function append(Model $model, string $key, mixed $value, ?Model $connected = null): array
    {
        $current = $this->get($model, $key, [], $connected);
        
        if (!is_array($current)) {
            $current = [$current];
        }
        
        $current[] = $value;
        $this->set($model, $key, $current, $connected);
        
        return $current;
    }

    public function prepend(Model $model, string $key, mixed $value, ?Model $connected = null): array
    {
        $current = $this->get($model, $key, [], $connected);
        
        if (!is_array($current)) {
            $current = [$current];
        }
        
        array_unshift($current, $value);
        $this->set($model, $key, $current, $connected);
        
        return $current;
    }

    public function removeFromArray(Model $model, string $key, mixed $value, ?Model $connected = null): array
    {
        $current = $this->get($model, $key, [], $connected);
        
        if (!is_array($current)) {
            return [];
        }
        
        $filtered = array_filter($current, fn($item) => $item !== $value);
        $result = array_values($filtered); // Reindex array
        
        $this->set($model, $key, $result, $connected);
        
        return $result;
    }

    private function createIdentifier(Model $model, Model|string|null $connected = null): MetaIdentifier
    {
        if (is_string($connected)) {
            return MetaIdentifier::fromModelWithType($model, $connected);
        }
        
        return MetaIdentifier::fromModel($model, $connected);
    }
}
