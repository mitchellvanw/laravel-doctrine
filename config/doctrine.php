<?php

use Mitch\LaravelDoctrine\Cache;


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

    // Uncomment the cache_provider you want to use in 'cache_providers' below,
    // and then put its name here.
    'cache_provider' => null,

    'cache_providers' => [
//        new Cache\ApcProvider,
//        new Cache\MemcacheProvider,
//        new Cache\RedisProvider,
//        new Cache\XcacheProvider,
//        new Cache\NullProvider,
    ],

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
