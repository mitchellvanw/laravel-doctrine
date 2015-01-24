<?php namespace Tests\MultipleEntityManagersAndConnections;

use Mitch\LaravelDoctrine\IlluminateRegistry;

class DatabaseMappingTest extends AbstractDatabaseMappingTest
{
    public function testDefaultDatabaseMapping()
    {
        $laravelDBConfig = $this->getLaravelDBConfig();
        $basicDoctrineConfig = $this->getBasicDoctrineConfiguration();

        $doctrineEntityConfig = $this->callMethod(
            $this->sp,
            'mapEntityManagers',
            array($basicDoctrineConfig, $laravelDBConfig['default'])
        );

        $this->assertEquals($laravelDBConfig['default'], $doctrineEntityConfig['default_connection']);
        $this->assertArrayHasKey('entity_managers', $doctrineEntityConfig);
        $this->assertArrayHasKey($laravelDBConfig['default'], $doctrineEntityConfig['entity_managers']);
        $this->assertSame(
            $basicDoctrineConfig['metadata'],
            $doctrineEntityConfig['entity_managers'][$laravelDBConfig['default']]['metadata']
        );
    }


    public function testCreateManagerInstances()
    {
        $laravelDBConfig = $this->getLaravelDBConfig();
        $basicDoctrineConfig = $this->getBasicDoctrineConfiguration();

        $basicDoctrineConfig['default_connection'] = 'pgsql';
        $basicDoctrineConfig['entity_managers'] = [
            'pgsqlEntityManager' => [
                'connection' => 'pgsql',
                'metadata' => $basicDoctrineConfig['metadata']
            ]
        ];

        $doctrineEntityConfig = $this->callMethod(
            $this->sp,
            'mapEntityManagers',
            array($basicDoctrineConfig, $laravelDBConfig['default'])
        );

        list($registryConnections, $registryManagers) = $this->callMethod(
            $this->sp,
            'createManagerInstances',
            array(
                $doctrineEntityConfig,
                $laravelDBConfig['connections'],
                false,
                $this->createCacheManager($basicDoctrineConfig['cache'])
            )
        );

        $this->assertArrayHasKey('pgsql', $registryConnections);
        $this->assertArrayHasKey('pgsqlEntityManager', $registryManagers);
    }

    public function testRegistryInstances()
    {
        $laravelDBConfig = $this->getLaravelDBConfig();
        $basicDoctrineConfig = $this->getBasicDoctrineConfiguration();

        $doctrineEntityConfig = $this->callMethod(
            $this->sp,
            'mapEntityManagers',
            array($basicDoctrineConfig, $laravelDBConfig['default'])
        );

        list($registryConnections, $registryManagers) = $this->callMethod(
            $this->sp,
            'createManagerInstances',
            array(
                $doctrineEntityConfig,
                $laravelDBConfig['connections'],
                false,
                $this->createCacheManager($basicDoctrineConfig['cache'])
            )
        );

        $registry = new IlluminateRegistry(
            $this->container,
            $registryConnections,
            $registryManagers
        );

        $em = $registry->getManager('mysql');

        $this->assertSame($em, $registry->getManager());
        $this->assertSame($em, $registry->getManager('default'));
        $this->assertSame($em, $this->container->make('doctrine.orm.mysql_entity_manager'));
        $this->assertSame($em, $this->container->make('doctrine.orm.default_entity_manager'));

        $con = $registry->getConnection('mysql');
        $this->assertSame($con, $registry->getConnection());
        $this->assertSame($con, $registry->getConnection('default'));
        $this->assertSame($con, $this->container->make('doctrine.dbal.mysql_connection'));
        $this->assertSame($con, $this->container->make('doctrine.dbal.default_connection'));
    }

    public function testUndefinedConnection()
    {
        $laravelDBConfig = $this->getLaravelDBConfig();
        $basicDoctrineConfig = $this->getBasicDoctrineConfiguration();

        $laravelDBConfig['default'] = 'misconfigure';

        $doctrineEntityConfig = $this->callMethod(
            $this->sp,
            'mapEntityManagers',
            array($basicDoctrineConfig, $laravelDBConfig['default'])
        );

        list($registryConnections, $registryManagers) = $this->callMethod(
            $this->sp,
            'createManagerInstances',
            array(
                $doctrineEntityConfig,
                $laravelDBConfig['connections'],
                false,
                $this->createCacheManager($basicDoctrineConfig['cache'])
            )
        );

        $this->assertArrayNotHasKey('misconfigure', $registryConnections);
    }
}
