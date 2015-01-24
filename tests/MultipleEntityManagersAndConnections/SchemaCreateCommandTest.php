<?php namespace Tests\MultipleEntityManagersAndConnections;

use Mitch\LaravelDoctrine\IlluminateRegistry;
use Doctrine\ORM\Tools\SchemaTool;
use Mitch\LaravelDoctrine\Console\SchemaCreateCommand;
use Mitch\LaravelDoctrine\Console\SchemaDropCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

class SchemaCreateCommandTest extends AbstractDatabaseMappingTest
{
    protected $expected;

    public function setup()
    {
        parent::setup();
        $this->expected = file_get_contents(__DIR__.DIRECTORY_SEPARATOR.'ExpectedOutput'.DIRECTORY_SEPARATOR.'SchemaCreateCommandTestOutput.txt');
    }

    public function testDefaultCreate()
    {
        $laravelDBConfig = $this->getLaravelDBConfig();
        $basicDoctrineConfig = $this->getBasicDoctrineConfiguration();

        $laravelDBConfig['default'] = 'sqlite';

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

        $command = new SchemaCreateCommand($registry);
        $command->setLaravel($this->container);
        $input = new ArrayInput(['--sql' => null]);
        $output = new BufferedOutput();

        $command->run($input, $output);

        $this->assertEquals(
            $this->expected,
            $output->fetch()
        );

        $command = new SchemaCreateCommand($registry);
        $command->setLaravel($this->container);
        $input = new ArrayInput([]);
        $command->run($input, $output);

        $command = new SchemaDropCommand($registry);
        $command->setLaravel($this->container);
        $command->run($input, $output);
    }

    public function testSpecificCreate()
    {
        $laravelDBConfig = $this->getLaravelDBConfig();
        $basicDoctrineConfig = $this->getBasicDoctrineConfiguration();

        $basicDoctrineConfig['entity_managers'] = [
            'pgsql' => [
                'connection' => 'pgsql',
                'metadata' => $basicDoctrineConfig['metadata']
            ],
            'mysql' => [
                'connection' => 'mysql',
                'metadata' => $basicDoctrineConfig['metadata']
            ],
            'sqlite' => [
                'connection' => 'sqlite',
                'metadata' => $basicDoctrineConfig['metadata']
            ],

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

        $registry = new IlluminateRegistry(
            $this->container,
            $registryConnections,
            $registryManagers
        );

        $command = new SchemaCreateCommand($registry);
        $command->setLaravel($this->container);
        $input = new ArrayInput(['--sql' => null, '--em' => 'sqlite']);
        $output = new BufferedOutput();

        $command->run($input, $output);

        $this->assertEquals(
            $this->expected,
            $output->fetch()
        );

        $command = new SchemaCreateCommand($registry);
        $command->setLaravel($this->container);
        $input = new ArrayInput(['--em' => 'sqlite']);
        $command->run($input, $output);

        $command = new SchemaDropCommand($registry);
        $command->setLaravel($this->container);
        $command->run($input, $output);
    }
}
