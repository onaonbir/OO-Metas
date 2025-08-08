<?php

declare(strict_types=1);

namespace OnaOnbir\OOMetas\Contracts;

use OnaOnbir\OOMetas\ValueObjects\MetaIdentifier;
use OnaOnbir\OOMetas\ValueObjects\MetaKey;

interface MetaCacheInterface
{
    /**
     * Get a cached meta value.
     */
    public function get(MetaIdentifier $identifier, MetaKey $key): mixed;

    /**
     * Set a cached meta value.
     */
    public function set(MetaIdentifier $identifier, MetaKey $key, mixed $value, ?int $ttl = null): bool;

    /**
     * Remove a cached meta value.
     */
    public function forget(MetaIdentifier $identifier, MetaKey $key): bool;

    /**
     * Remove all cached meta values for a given identifier.
     */
    public function forgetByIdentifier(MetaIdentifier $identifier): bool;

    /**
     * Check if a meta value is cached.
     */
    public function has(MetaIdentifier $identifier, MetaKey $key): bool;

    /**
     * Clear all cached meta values.
     */
    public function flush(): bool;

    /**
     * Generate cache key for meta.
     */
    public function getCacheKey(MetaIdentifier $identifier, MetaKey $key): string;

    /**
     * Get cache prefix.
     */
    public function getPrefix(): string;

    /**
     * Set cache prefix.
     */
    public function setPrefix(string $prefix): void;
}
