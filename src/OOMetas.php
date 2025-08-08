<?php

declare(strict_types=1);

namespace OnaOnbir\OOMetas;

use Illuminate\Database\Eloquent\Model;
use OnaOnbir\OOMetas\Contracts\MetaServiceInterface;

/**
 * OOMetas Facade
 *
 * This class provides a static interface to the MetaService
 * for backward compatibility and convenience.
 */
class OOMetas
{
    protected static ?MetaServiceInterface $service = null;

    protected static function getService(): MetaServiceInterface
    {
        if (self::$service === null) {
            self::$service = app(MetaServiceInterface::class);
        }

        return self::$service;
    }

    public static function setService(MetaServiceInterface $service): void
    {
        self::$service = $service;
    }

    // Basic operations
    public static function get(Model $model, string $key, mixed $default = null, Model|string|null $connected = null): mixed
    {
        return self::getService()->get($model, $key, $default, $connected);
    }

    public static function set(Model $model, string $key, mixed $value, ?Model $connected = null): void
    {
        self::getService()->set($model, $key, $value, $connected);
    }

    public static function forget(Model $model, string $key, ?Model $connected = null): void
    {
        self::getService()->forget($model, $key, $connected);
    }

    public static function has(Model $model, string $key, Model|string|null $connected = null): bool
    {
        return self::getService()->has($model, $key, $connected);
    }

    // Batch operations
    public static function getMany(Model $model, array $keys, Model|string|null $connected = null): array
    {
        return self::getService()->getMany($model, $keys, $connected);
    }

    public static function setMany(Model $model, array $values, ?Model $connected = null): void
    {
        self::getService()->setMany($model, $values, $connected);
    }

    public static function forgetMany(Model $model, array $keys, ?Model $connected = null): void
    {
        self::getService()->forgetMany($model, $keys, $connected);
    }

    public static function getAll(Model $model, ?Model $connected = null): array
    {
        return self::getService()->getAll($model, $connected);
    }

    public static function forgetAll(Model $model, ?Model $connected = null): void
    {
        self::getService()->forgetAll($model, $connected);
    }

    // Numeric operations
    public static function increment(Model $model, string $key, int $value = 1, ?Model $connected = null): int
    {
        return self::getService()->increment($model, $key, $value, $connected);
    }

    public static function decrement(Model $model, string $key, int $value = 1, ?Model $connected = null): int
    {
        return self::getService()->decrement($model, $key, $value, $connected);
    }

    // Special operations
    public static function pull(Model $model, string $key, mixed $default = null, ?Model $connected = null): mixed
    {
        return self::getService()->pull($model, $key, $default, $connected);
    }

    public static function remember(Model $model, string $key, callable $callback, ?Model $connected = null): mixed
    {
        return self::getService()->remember($model, $key, $callback, $connected);
    }

    public static function toggle(Model $model, string $key, ?Model $connected = null): bool
    {
        return self::getService()->toggle($model, $key, $connected);
    }

    // Array operations
    public static function append(Model $model, string $key, mixed $value, ?Model $connected = null): array
    {
        return self::getService()->append($model, $key, $value, $connected);
    }

    public static function prepend(Model $model, string $key, mixed $value, ?Model $connected = null): array
    {
        return self::getService()->prepend($model, $key, $value, $connected);
    }

    public static function removeFromArray(Model $model, string $key, mixed $value, ?Model $connected = null): array
    {
        return self::getService()->removeFromArray($model, $key, $value, $connected);
    }
}
