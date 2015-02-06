<?php

return [
    'simple_annotations' => false,

    'metadata' => [
        base_path('app/models')
    ],

    'proxy' => [
        'auto_generate' => true,
        'directory'     => 'Proxies',
        'namespace'     => 'Proxy'
    ],

    // Available: null, apc, xcache, redis, memcache
    'cache_provider' => null,

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

    'logger' => null,

    // Add here the classes with your Doctrine event listeners
    'events' => [
        'subscribers' => [
            'Gedmo\Tree\TreeListener',
            'Gedmo\Timestampable\TimestampableListener',
            'Gedmo\Sluggable\SluggableListener',
            'Gedmo\Loggable\LoggableListener',
            'Gedmo\Sortable\SortableListener',
            'Gedmo\Translatable\TranslatableListener',
        ],
    ],

    // Add the annotation drivers
    'annotations' => [
        'drivers' => [
            [
                'class' => '\Gedmo\DoctrineExtensions',
                'method' => 'registerAnnotations',
            ],
        ],
    ],
];
