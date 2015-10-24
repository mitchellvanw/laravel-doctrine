<?php

return [
    'simple_annotations' => false,

    'metadata' => [
        base_path('app/models')
    ],

    'proxy' => [
        'auto_generate' => false,
        'directory'     => null,
        'namespace'     => null
    ],

    // Available: null, apc, xcache, redis, memcache
    'cache_provider' => null,

    // A string that will act as a prefix for cached values' keys.
    // When left as `null`, this will be internally defaulted to:
    // "dc2_" . md5($proxyDir) . "_", see: Doctrine\ORM\Tools\Setup
    'cache_key_namespace' => null,

    // When set to `null`, then the doctrine's query result cache will be the same
    // as the metadata and query caches. If you need to be able to flush query result
    // cache without touching the metadata and query caches, then set the following
    // namespace to something different from the value in the `cache_key_namespace`
    // config.
    'result_cache_key_namespace' => null,

    'cache' => [
        'redis' => [
            'host'     => '127.0.0.1',
            'port'     => 6379,
            'database' => 1
        ],
        'memcache' => [
            'host' => '127.0.0.1',
            'port' => 11211
        ]
    ],

    'repository' => 'Doctrine\ORM\EntityRepository',

    'repositoryFactory' => null,

    'logger' => null
];
