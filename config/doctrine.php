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
