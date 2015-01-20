<?php namespace Tests\MultipleEntityManagersAndConnections;

use Mitch\LaravelDoctrine\IlluminateRegistry;
use Doctrine\ORM\Tools\SchemaTool;

class SchemaCreateCommandTest extends AbstractDatabaseMappingTest
{

    public function testCreateSchemaSql()
    {
        $laravelDBConfig = $this->getLaravelDBConfig();
        $basicDoctrineConfig = $this->getBasicDoctrineConfiguration();

        $laravelDBConfig['default'] = 'sqlite';

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

        $registry = new IlluminateRegistry(
            $this->containerStub,
            $registryConnections,
            $registryManagers,
            $defaults['connection'],
            $defaults['entityManager']
        );

        foreach ($registry->getManagerNames() as $key => $value) {
            $manager = $registry->getManager($key);
            $tool = new SchemaTool($manager);
            $sql = $tool->getCreateSchemaSql($manager->getMetadataFactory()->getAllMetadata());

            $this->assertEquals(
                'CREATE TABLE user (id INTEGER NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))',
                implode(';'.PHP_EOL, $sql)
            );
        }

    }
}
