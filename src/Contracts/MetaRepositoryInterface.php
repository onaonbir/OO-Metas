<?php

declare(strict_types=1);

namespace OnaOnbir\OOMetas\Contracts;

use Illuminate\Database\Eloquent\Collection;
use OnaOnbir\OOMetas\ValueObjects\MetaIdentifier;
use OnaOnbir\OOMetas\ValueObjects\MetaKey;
use OnaOnbir\OOMetas\Models\Meta;

interface MetaRepositoryInterface
{
    /**
     * Find a meta record by identifier and key.
     */
    public function find(MetaIdentifier $identifier, MetaKey $key): ?Meta;

    /**
     * Get multiple meta records by identifier and keys.
     *
     * @param array<MetaKey> $keys
     * @return Collection<Meta>
     */
    public function findMany(MetaIdentifier $identifier, array $keys): Collection;

    /**
     * Get all meta records for a given identifier.
     */
    public function findByIdentifier(MetaIdentifier $identifier): Collection;

    /**
     * Create or update a meta record.
     */
    public function save(MetaIdentifier $identifier, MetaKey $key, mixed $value): Meta;

    /**
     * Create or update multiple meta records.
     *
     * @param array<string, mixed> $data
     * @return Collection<Meta>
     */
    public function saveMany(MetaIdentifier $identifier, array $data): Collection;

    /**
     * Delete a meta record.
     */
    public function delete(MetaIdentifier $identifier, MetaKey $key): bool;

    /**
     * Delete multiple meta records.
     *
     * @param array<MetaKey> $keys
     */
    public function deleteMany(MetaIdentifier $identifier, array $keys): int;

    /**
     * Delete all meta records for a given identifier.
     */
    public function deleteByIdentifier(MetaIdentifier $identifier): int;

    /**
     * Check if a meta record exists.
     */
    public function exists(MetaIdentifier $identifier, MetaKey $key): bool;

    /**
     * Count meta records for a given identifier.
     */
    public function count(MetaIdentifier $identifier): int;
}
