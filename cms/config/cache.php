<?php

return [
    // Cache driver (file, redis, memcached)
    'default' => env('CACHE_DRIVER', 'file'),

    'stores' => [
        'file' => [
            'driver' => 'file',
            'path' => storage_path('framework/cache/data'),
        ],
    ],

    // Cache key prefix
    'prefix' => env('CACHE_PREFIX', 'kwikster_cache'),
];
