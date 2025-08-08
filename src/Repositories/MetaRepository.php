<?php

declare(strict_types=1);

namespace OnaOnbir\OOMetas\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;
use OnaOnbir\OOMetas\Contracts\MetaRepositoryInterface;
use OnaOnbir\OOMetas\Models\Meta;
use OnaOnbir\OOMetas\ValueObjects\MetaIdentifier;
use OnaOnbir\OOMetas\ValueObjects\MetaKey;

class MetaRepository implements MetaRepositoryInterface
{
    public function find(MetaIdentifier $identifier, MetaKey $key): ?Meta
    {
        return $this->buildQuery($identifier, $key)->first();
    }

    public function findMany(MetaIdentifier $identifier, array $keys): Collection
    {
        $mainKeys = array_unique(
            array_map(fn (MetaKey $key) => $key->getMainKey(), $keys)
        );

        return $this->buildBaseQuery($identifier)
            ->whereIn('key', $mainKeys)
            ->get();
    }

    public function findByIdentifier(MetaIdentifier $identifier): Collection
    {
        return $this->buildBaseQuery($identifier)->get();
    }

    public function save(MetaIdentifier $identifier, MetaKey $key, mixed $value): Meta
    {
        $meta = $this->buildQuery($identifier, $key)->first();

        if (! $meta) {
            $meta = new Meta;
            $meta->fill($identifier->toArray());
            $meta->key = $key->getMainKey();
        }

        if ($key->isNested()) {
            $currentValue = $meta->value ?? [];

            if (! is_array($currentValue)) {
                $currentValue = [];
            }

            data_set($currentValue, $key->getNestedKey(), $value);
            $meta->value = $currentValue;
        } else {
            $meta->value = $value;
        }

        $meta->save();

        return $meta;
    }

    public function saveMany(MetaIdentifier $identifier, array $data): Collection
    {
        $metas = [];

        foreach ($data as $keyString => $value) {
            $key = MetaKey::make($keyString);
            $metas[] = $this->save($identifier, $key, $value);
        }

        // Return Eloquent Collection instead of Support Collection
        return new Collection($metas);
    }

    public function delete(MetaIdentifier $identifier, MetaKey $key): bool
    {
        $meta = $this->buildQuery($identifier, $key)->first();

        if (! $meta) {
            return false;
        }

        if ($key->isNested()) {
            $currentValue = $meta->value;

            if (is_array($currentValue)) {
                Arr::forget($currentValue, $key->getNestedKey());

                // If the array becomes empty, delete the whole meta
                if (empty($currentValue)) {
                    return $meta->delete();
                }

                $meta->value = $currentValue;
                $meta->save();

                return true;
            }

            return false;
        }

        return $meta->delete();
    }

    public function deleteMany(MetaIdentifier $identifier, array $keys): int
    {
        $mainKeys = array_unique(
            array_map(fn (MetaKey $key) => $key->getMainKey(), $keys)
        );

        return $this->buildBaseQuery($identifier)
            ->whereIn('key', $mainKeys)
            ->delete();
    }

    public function deleteByIdentifier(MetaIdentifier $identifier): int
    {
        return $this->buildBaseQuery($identifier)->delete();
    }

    public function exists(MetaIdentifier $identifier, MetaKey $key): bool
    {
        $meta = $this->buildQuery($identifier, $key)->first();

        if (! $meta) {
            return false;
        }

        if ($key->isNested()) {
            return data_get($meta->value, $key->getNestedKey()) !== null;
        }

        return true;
    }

    public function count(MetaIdentifier $identifier): int
    {
        return $this->buildBaseQuery($identifier)->count();
    }

    protected function buildBaseQuery(MetaIdentifier $identifier)
    {
        $query = Meta::where('model_type', $identifier->modelType)
            ->where('model_id', $identifier->modelId);

        if ($identifier->hasFullConnection()) {
            $query->where('connected_type', $identifier->connectedType)
                ->where('connected_id', $identifier->connectedId);
        } elseif ($identifier->hasTypeOnlyConnection()) {
            $query->where('connected_type', $identifier->connectedType)
                ->whereNull('connected_id');
        } else {
            $query->whereNull('connected_type')
                ->whereNull('connected_id');
        }

        return $query;
    }

    protected function buildQuery(MetaIdentifier $identifier, MetaKey $key)
    {
        return $this->buildBaseQuery($identifier)
            ->where('key', $key->getMainKey());
    }

    protected function getQueryTimeout(): int
    {
        return config('oo-metas.performance.query_timeout', 30);
    }

    protected function getBatchSize(): int
    {
        return config('oo-metas.performance.batch_size', 100);
    }
}
