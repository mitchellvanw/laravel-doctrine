<?php

return [
    // Available: APC, Xcache, Redis, Memcache
    'provider' => null,

    'connection' => [
        'driver'   => 'pdo_mysql',
        'host'     => 'localhost',
        'database' => 'database',
        'username' => 'root',
        'password' => '',
        'prefix'   => ''
    ],

    'metadata' => [
        base_path('app/models')
    ],

    'proxy' => [
        'auto_generate' => false,
        'directory'     => null,
        'namespace'     => null
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

    'migrations' => [
        'directory' => 'database/doctrine-migrations',
        'table'     => 'doctrine_migrations'
    ],

    'repository' => 'Doctrine\ORM\EntityRepository',

    'logger' => null
];
