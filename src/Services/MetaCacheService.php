<?php

declare(strict_types=1);

namespace OnaOnbir\OOMetas\Services;

use Illuminate\Cache\Repository as CacheRepository;
use OnaOnbir\OOMetas\Contracts\MetaCacheInterface;
use OnaOnbir\OOMetas\ValueObjects\MetaIdentifier;
use OnaOnbir\OOMetas\ValueObjects\MetaKey;

class MetaCacheService implements MetaCacheInterface
{
    private string $prefix = 'oo_metas';

    private ?int $defaultTtl = null;

    public function __construct(
        private CacheRepository $cache
    ) {
        $this->defaultTtl = config('oo-metas.cache.ttl');
    }

    public function get(MetaIdentifier $identifier, MetaKey $key): mixed
    {
        $cacheKey = $this->getCacheKey($identifier, $key);

        return $this->cache->get($cacheKey);
    }

    public function set(MetaIdentifier $identifier, MetaKey $key, mixed $value, ?int $ttl = null): bool
    {
        $cacheKey = $this->getCacheKey($identifier, $key);
        $ttl = $ttl ?? $this->defaultTtl;

        if ($ttl === null) {
            return $this->cache->forever($cacheKey, $value);
        }

        return $this->cache->put($cacheKey, $value, $ttl);
    }

    public function forget(MetaIdentifier $identifier, MetaKey $key): bool
    {
        $cacheKey = $this->getCacheKey($identifier, $key);

        return $this->cache->forget($cacheKey);
    }

    public function forgetByIdentifier(MetaIdentifier $identifier): bool
    {
        $pattern = $this->getIdentifierPattern($identifier);

        return $this->cache->forget($pattern);
    }

    public function has(MetaIdentifier $identifier, MetaKey $key): bool
    {
        $cacheKey = $this->getCacheKey($identifier, $key);

        return $this->cache->has($cacheKey);
    }

    public function flush(): bool
    {
        return $this->cache->flush();
    }

    public function getCacheKey(MetaIdentifier $identifier, MetaKey $key): string
    {
        return $this->prefix.':'.$identifier->getFullSignature().':'.$key->getFullKey();
    }

    public function getPrefix(): string
    {
        return $this->prefix;
    }

    public function setPrefix(string $prefix): void
    {
        $this->prefix = $prefix;
    }

    private function getIdentifierPattern(MetaIdentifier $identifier): string
    {
        return $this->prefix.':'.$identifier->getFullSignature().':*';
    }
}
