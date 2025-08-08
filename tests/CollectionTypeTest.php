<?php

declare(strict_types=1);

namespace OnaOnbir\OOMetas\Tests;

use Illuminate\Database\Eloquent\Collection;
use OnaOnbir\OOMetas\Repositories\MetaRepository;
use OnaOnbir\OOMetas\ValueObjects\MetaIdentifier;
use OnaOnbir\OOMetas\ValueObjects\MetaKey;

/**
 * Simple test script to verify the collection type fixes
 */
class CollectionTypeTest
{
    public function testSaveManyReturnsEloquentCollection()
    {
        // This would be in a real test with proper setup
        $repository = new MetaRepository;

        // Mock identifier and data
        $identifier = new MetaIdentifier(
            modelType: 'App\\User',
            modelId: '1'
        );

        $data = [
            'theme' => 'dark',
            'language' => 'en',
        ];

        // This should now return Illuminate\Database\Eloquent\Collection
        // instead of Illuminate\Support\Collection
        $result = $repository->saveMany($identifier, $data);

        // Verify it's the correct type
        echo 'Result type: '.get_class($result)."\n";
        echo 'Is Eloquent Collection: '.($result instanceof Collection ? 'Yes' : 'No')."\n";

        return $result instanceof Collection;
    }

    public function testFindManyReturnsEloquentCollection()
    {
        $repository = new MetaRepository;

        $identifier = new MetaIdentifier(
            modelType: 'App\\User',
            modelId: '1'
        );

        $keys = [
            MetaKey::make('theme'),
            MetaKey::make('language'),
        ];

        // This should return Illuminate\Database\Eloquent\Collection
        $result = $repository->findMany($identifier, $keys);

        echo 'FindMany result type: '.get_class($result)."\n";
        echo 'Is Eloquent Collection: '.($result instanceof Collection ? 'Yes' : 'No')."\n";

        return $result instanceof Collection;
    }
}

// If run directly
if (php_sapi_name() === 'cli' && isset($argv[0]) && basename($argv[0]) === basename(__FILE__)) {
    echo "Testing Collection Types...\n";
    echo "This test requires proper Laravel environment setup.\n";
    echo "The fixes ensure Eloquent Collections are returned instead of Support Collections.\n";
}
