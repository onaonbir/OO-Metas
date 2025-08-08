<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Table Names
    |--------------------------------------------------------------------------
    |
    | This section allows you to customize the table names used by the
    | OOMetas package. You can change the table name to fit your
    | application's naming conventions.
    |
    */
    'table_names' => [
        'oo_metas' => env('OO_METAS_TABLE_NAME', 'oo_metas'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Cache Configuration
    |--------------------------------------------------------------------------
    |
    | This section configures the caching behavior of the meta system.
    | You can enable/disable caching and set cache TTL values.
    |
    */
    'cache' => [
        'enabled' => env('OO_METAS_CACHE_ENABLED', true),
        'ttl' => env('OO_METAS_CACHE_TTL', 3600), // 1 hour in seconds
        'prefix' => env('OO_METAS_CACHE_PREFIX', 'oo_metas'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Validation Configuration
    |--------------------------------------------------------------------------
    |
    | This section configures validation rules for meta keys and values.
    |
    */
    'validation' => [
        'key' => [
            'max_length' => 255,
            'allowed_characters' => '/^[\w\-\.]+$/', // Only alphanumeric, dash, underscore, dot
        ],
        'value' => [
            'max_depth' => 10, // Maximum nesting depth for arrays/objects
            'max_size' => 1024 * 1024, // Maximum size in bytes (1MB)
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Performance Configuration
    |--------------------------------------------------------------------------
    |
    | This section configures performance-related settings.
    |
    */
    'performance' => [
        'batch_size' => 100, // Maximum number of items in batch operations
        'query_timeout' => 30, // Query timeout in seconds
    ],

];
