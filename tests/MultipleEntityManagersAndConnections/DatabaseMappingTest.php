<?php namespace Tests\MultipleEntityManagersAndConnections;

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

        list($registryConnections, $registryManagers, $defaults) = $this->callMethod(
            $this->sp,
            'createManagerInstances',
            array(
                $doctrineEntityConfig,
                $laravelDBConfig['connections'],
                false,
                $this->createCacheManager($basicDoctrineConfig['cache'])
            )
        );

        $this->assertEquals('pgsql', $defaults['connection']);
        $this->assertEquals('pgsqlEntityManager', $defaults['entityManager']);
        $this->assertArrayHasKey('pgsql', $registryConnections);
        $this->assertArrayHasKey('pgsqlEntityManager', $registryManagers);

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
