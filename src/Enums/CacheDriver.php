<?php

declare(strict_types=1);

namespace OnaOnbir\OOMetas\Enums;

enum CacheDriver: string
{
    case REDIS = 'redis';
    case MEMCACHED = 'memcached';
    case DATABASE = 'database';
    case FILE = 'file';
    case ARRAY = 'array';
    case NULL = 'null';

    public function getDescription(): string
    {
        return match($this) {
            self::REDIS => 'Redis cache driver',
            self::MEMCACHED => 'Memcached cache driver',
            self::DATABASE => 'Database cache driver',
            self::FILE => 'File cache driver',
            self::ARRAY => 'Array cache driver (memory only)',
            self::NULL => 'Null cache driver (no caching)',
        };
    }

    public function isDistributed(): bool
    {
        return match($this) {
            self::REDIS, self::MEMCACHED, self::DATABASE => true,
            default => false,
        };
    }

    public function isPersistent(): bool
    {
        return match($this) {
            self::REDIS, self::MEMCACHED, self::DATABASE, self::FILE => true,
            default => false,
        };
    }
}
