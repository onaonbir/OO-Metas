<?php

namespace OnaOnbir\OOMetas;

use Illuminate\Support\Arr;
use OnaOnbir\OOMetas\Models\Meta;

class OOMetas
{
    public static function get(object $model, string $key, mixed $default = null, object|string|null $connected = null): mixed
    {
        [$mainKey, $nestedKey] = self::splitKey($key);

        $baseQuery = Meta::where('model_type', get_class($model))
            ->where('model_id', (string) $model->getKey())
            ->where('key', $mainKey);

        // eğer connected model verildiyse (object)
        if (is_object($connected)) {
            $query = (clone $baseQuery)
                ->where('connected_type', get_class($connected))
                ->where('connected_id', (string) $connected->getKey());

            $meta = $query->first();

            if ($meta) {
                return is_null($nestedKey)
                    ? $meta->value
                    : data_get($meta->value, $nestedKey, $default);
            }
        }

        // eğer connected string olarak verildiyse (örn. Workspace::class)
        if (is_string($connected)) {
            $query = (clone $baseQuery)
                ->where('connected_type', $connected);

            $meta = $query->first();

            if ($meta) {
                return is_null($nestedKey)
                    ? $meta->value
                    : data_get($meta->value, $nestedKey, $default);
            }
        }

        // fallback: connected null olan değer
        $meta = (clone $baseQuery)
            ->whereNull('connected_type')
            ->whereNull('connected_id')
            ->first();

        if (! $meta) {
            return $default;
        }

        return is_null($nestedKey)
            ? $meta->value
            : data_get($meta->value, $nestedKey, $default);
    }

    public static function set(object $model, string $key, mixed $value, ?object $connected = null): void
    {
        [$mainKey, $nestedKey] = self::splitKey($key);

        $query = Meta::firstOrNew([
            'model_type' => get_class($model),
            'model_id' => $model->getKey(),
            'key' => $mainKey,
            'connected_type' => $connected ? get_class($connected) : null,
            'connected_id' => $connected ? $connected->getKey() : null,
        ]);

        if (is_null($nestedKey)) {
            // Direkt değer atama
            $query->value = $value;
        } else {
            // Nested key için mevcut veriyi koru ve merge et
            $data = $query->value ?? [];

            // Eğer mevcut value array değilse boş array yap
            if (! is_array($data)) {
                $data = [];
            }

            data_set($data, $nestedKey, $value);
            $query->value = $data;
        }

        $query->save();
    }

    public static function forget(object $model, string $key, ?object $connected = null): void
    {
        [$mainKey, $nestedKey] = self::splitKey($key);

        $baseQuery = Meta::where('model_type', get_class($model))
            ->where('model_id', $model->getKey())
            ->where('key', $mainKey);

        // Connected parametresi için query'yi düzenle
        if (is_object($connected)) {
            $query = (clone $baseQuery)
                ->where('connected_type', get_class($connected))
                ->where('connected_id', $connected->getKey());
        } else {
            $query = (clone $baseQuery)
                ->whereNull('connected_type')
                ->whereNull('connected_id');
        }

        $meta = $query->first();

        if (! $meta) {
            return;
        }

        if (is_null($nestedKey)) {
            $meta->delete();
        } else {
            $data = $meta->value;
            if (is_array($data)) {
                Arr::forget($data, $nestedKey);
                $meta->value = $data;
                $meta->save();
            }
        }
    }

    public static function has(object $model, string $key, object|string|null $connected = null): bool
    {
        [$mainKey, $nestedKey] = self::splitKey($key);

        $baseQuery = Meta::where('model_type', get_class($model))
            ->where('model_id', (string) $model->getKey())
            ->where('key', $mainKey);

        if (is_object($connected)) {
            $query = (clone $baseQuery)
                ->where('connected_type', get_class($connected))
                ->where('connected_id', (string) $connected->getKey());
        } elseif (is_string($connected)) {
            $query = (clone $baseQuery)
                ->where('connected_type', $connected);
        } else {
            $query = (clone $baseQuery)
                ->whereNull('connected_type')
                ->whereNull('connected_id');
        }

        $meta = $query->first();

        if (! $meta) {
            return false;
        }

        if (is_null($nestedKey)) {
            return true;
        }

        return data_get($meta->value, $nestedKey) !== null;
    }

    public static function increment(object $model, string $key, int $value = 1, ?object $connected = null): int
    {
        $current = self::get($model, $key, 0, $connected);
        $newValue = (int) $current + $value;
        self::set($model, $key, $newValue, $connected);

        return $newValue;
    }

    public static function decrement(object $model, string $key, int $value = 1, ?object $connected = null): int
    {
        return self::increment($model, $key, -$value, $connected);
    }

    public static function pull(object $model, string $key, mixed $default = null, ?object $connected = null): mixed
    {
        $value = self::get($model, $key, $default, $connected);
        self::forget($model, $key, $connected);

        return $value;
    }

    public static function remember(object $model, string $key, callable $callback, ?object $connected = null): mixed
    {
        if (self::has($model, $key, $connected)) {
            return self::get($model, $key, null, $connected);
        }

        $value = $callback();
        self::set($model, $key, $value, $connected);

        return $value;
    }

    protected static function splitKey(string $key): array
    {
        return str_contains($key, '.')
            ? explode('.', $key, 2)
            : [$key, null];
    }
}
