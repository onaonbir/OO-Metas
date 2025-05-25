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

        if (! is_array($query->value)) {
            $query->value = [];
        }

        if (is_null($nestedKey)) {
            $query->value = $value;
        } else {
            $data = $query->value ?? [];
            data_set($data, $nestedKey, $value);
            $query->value = $data;
        }

        $query->save();
    }

    public static function forget(object $model, string $key): void
    {
        [$mainKey, $nestedKey] = self::splitKey($key);

        $query = Meta::where('model_type', get_class($model))
            ->where('model_id', $model->getKey())
            ->where('key', $mainKey);

        $meta = $query->first();

        if (! $meta) {
            return;
        }

        if (is_null($nestedKey)) {
            $meta->delete();
        } else {
            $data = $meta->value;
            Arr::forget($data, $nestedKey);
            $meta->value = $data;
            $meta->save();
        }
    }

    protected static function splitKey(string $key): array
    {
        return str_contains($key, '.')
            ? explode('.', $key, 2)
            : [$key, null];
    }
}
