<?php

return [
    /** @deprecated use 'mappings.params' and set the type to 'annotations */
    'simple_annotations' => false,
    /** @deprecated use 'mapping.params' */
    'metadata' => [
        base_path('app/models')
    ],
    /**
     * Mapping configuration. Allows for any supported doctrine mapping, including your own!
     */
    'mappings' => [
        /**
         * One of xml, yml, annotations, static_php or a custom key set below
         */
        'type' => 'annotations',
        /**
         * Array of params passed to the driver's constructor
         */
        'params' => [
            /**
             * All default doctine drivers expect first param to be an array of paths
             */
            [base_path('app/models')]
            /**
             * Further params may be required (ie. annotations require the simple_annotations boolean here)
             */
        ],
        /**
         * Add custom drivers as $key => callable $factory here.
         */
        'custom_drivers' => [
            /**
             * Note that $factory is a php callable, currently "object@method" is not supported
             */
        ]
    ],

    'proxy' => [
        'auto_generate' => false,
        'directory'     => null,
        'namespace'     => null
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

    'logger' => null
];
