<?php

declare(strict_types=1);

namespace OnaOnbir\OOMetas\Contracts;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

interface MetaServiceInterface
{
    /**
     * Get a meta value.
     */
    public function get(Model $model, string $key, mixed $default = null, Model|string|null $connected = null): mixed;

    /**
     * Get multiple meta values.
     *
     * @param array<string> $keys
     * @return array<string, mixed>
     */
    public function getMany(Model $model, array $keys, Model|string|null $connected = null): array;

    /**
     * Get all meta values for a model.
     *
     * @return array<string, mixed>
     */
    public function getAll(Model $model, Model|string|null $connected = null): array;

    /**
     * Set a meta value.
     */
    public function set(Model $model, string $key, mixed $value, ?Model $connected = null): void;

    /**
     * Set multiple meta values.
     *
     * @param array<string, mixed> $values
     */
    public function setMany(Model $model, array $values, ?Model $connected = null): void;

    /**
     * Forget a meta value.
     */
    public function forget(Model $model, string $key, ?Model $connected = null): void;

    /**
     * Forget multiple meta values.
     *
     * @param array<string> $keys
     */
    public function forgetMany(Model $model, array $keys, ?Model $connected = null): void;

    /**
     * Forget all meta values for a model.
     */
    public function forgetAll(Model $model, ?Model $connected = null): void;

    /**
     * Check if a meta value exists.
     */
    public function has(Model $model, string $key, Model|string|null $connected = null): bool;

    /**
     * Increment a meta value.
     */
    public function increment(Model $model, string $key, int $value = 1, ?Model $connected = null): int;

    /**
     * Decrement a meta value.
     */
    public function decrement(Model $model, string $key, int $value = 1, ?Model $connected = null): int;

    /**
     * Get and forget a meta value.
     */
    public function pull(Model $model, string $key, mixed $default = null, ?Model $connected = null): mixed;

    /**
     * Remember a meta value using a callback.
     */
    public function remember(Model $model, string $key, callable $callback, ?Model $connected = null): mixed;

    /**
     * Toggle a boolean meta value.
     */
    public function toggle(Model $model, string $key, ?Model $connected = null): bool;

    /**
     * Append to an array meta value.
     */
    public function append(Model $model, string $key, mixed $value, ?Model $connected = null): array;

    /**
     * Prepend to an array meta value.
     */
    public function prepend(Model $model, string $key, mixed $value, ?Model $connected = null): array;

    /**
     * Remove from an array meta value.
     */
    public function removeFromArray(Model $model, string $key, mixed $value, ?Model $connected = null): array;
}
