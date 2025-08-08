<?php

declare(strict_types=1);

namespace OnaOnbir\OOMetas\Repositories;

use Illuminate\Database\Eloquent\Collection;
use OnaOnbir\OOMetas\Contracts\MetaCacheInterface;
use OnaOnbir\OOMetas\Contracts\MetaRepositoryInterface;
use OnaOnbir\OOMetas\Models\Meta;
use OnaOnbir\OOMetas\ValueObjects\MetaIdentifier;
use OnaOnbir\OOMetas\ValueObjects\MetaKey;

class CachedMetaRepository implements MetaRepositoryInterface
{
    public function __construct(
        private MetaRepositoryInterface $repository,
        private MetaCacheInterface $cache
    ) {}

    public function find(MetaIdentifier $identifier, MetaKey $key): ?Meta
    {
        $cacheKey = $this->cache->getCacheKey($identifier, $key);
        
        return cache()->remember($cacheKey, config('oo-metas.cache.ttl', 3600), function () use ($identifier, $key) {
            return $this->repository->find($identifier, $key);
        });
    }

    public function findMany(MetaIdentifier $identifier, array $keys): Collection
    {
        // For batch operations, we'll skip cache to avoid complexity
        // Individual items will be cached when accessed via find()
        return $this->repository->findMany($identifier, $keys);
    }

    public function findByIdentifier(MetaIdentifier $identifier): Collection
    {
        // Skip cache for bulk operations
        return $this->repository->findByIdentifier($identifier);
    }

    public function save(MetaIdentifier $identifier, MetaKey $key, mixed $value): Meta
    {
        $meta = $this->repository->save($identifier, $key, $value);
        
        // Update cache with the new value
        $this->cache->set($identifier, $key, $meta);
        
        return $meta;
    }

    public function saveMany(MetaIdentifier $identifier, array $data): Collection
    {
        $metas = $this->repository->saveMany($identifier, $data);
        
        // Clear cache for the identifier to ensure consistency
        $this->cache->forgetByIdentifier($identifier);
        
        return $metas;
    }

    public function delete(MetaIdentifier $identifier, MetaKey $key): bool
    {
        $result = $this->repository->delete($identifier, $key);
        
        if ($result) {
            $this->cache->forget($identifier, $key);
        }
        
        return $result;
    }

    public function deleteMany(MetaIdentifier $identifier, array $keys): int
    {
        $result = $this->repository->deleteMany($identifier, $keys);
        
        if ($result > 0) {
            // Clear cache for all deleted keys
            foreach ($keys as $key) {
                $this->cache->forget($identifier, $key);
            }
        }
        
        return $result;
    }

    public function deleteByIdentifier(MetaIdentifier $identifier): int
    {
        $result = $this->repository->deleteByIdentifier($identifier);
        
        if ($result > 0) {
            $this->cache->forgetByIdentifier($identifier);
        }
        
        return $result;
    }

    public function exists(MetaIdentifier $identifier, MetaKey $key): bool
    {
        // Check cache first
        if ($this->cache->has($identifier, $key)) {
            $meta = $this->cache->get($identifier, $key);
            return $meta !== null;
        }
        
        return $this->repository->exists($identifier, $key);
    }

    public function count(MetaIdentifier $identifier): int
    {
        // Count operations are typically not cached due to frequent changes
        return $this->repository->count($identifier);
    }
}
