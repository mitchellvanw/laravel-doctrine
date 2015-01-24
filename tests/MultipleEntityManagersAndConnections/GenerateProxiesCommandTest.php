<?php namespace Tests\MultipleEntityManagersAndConnections;

use Mitch\LaravelDoctrine\IlluminateRegistry;
use Mitch\LaravelDoctrine\Console\GenerateProxiesCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

class GenerateProxiesCommandTest extends AbstractDatabaseMappingTest
{
    protected $expected;

    public function setUp()
    {
        parent::setup();
        $this->expected = file_get_contents(__DIR__.DIRECTORY_SEPARATOR.'ExpectedOutput'.DIRECTORY_SEPARATOR.'GenerateProxiesCommandTestOutput.txt');
    }

    public function testDefaultGenerate()
    {

        $laravelDBConfig = $this->getLaravelDBConfig();
        $basicDoctrineConfig = $this->getBasicDoctrineConfiguration();

        $laravelDBConfig['default'] = 'sqlite';
        $basicDoctrineConfig['proxy']['directory'] = sys_get_temp_dir();

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

        $command = new GenerateProxiesCommand($registry);
        $command->setLaravel($this->container);
        $input = new ArrayInput([]);
        $output = new BufferedOutput();

        $command->run($input, $output);

        $this->assertEquals(
            $this->expected,
            $output->fetch()
        );
    }

    public function testSpecificGenerate()
    {
        $laravelDBConfig = $this->getLaravelDBConfig();
        $basicDoctrineConfig = $this->getBasicDoctrineConfiguration();
        $basicDoctrineConfig['proxy']['directory'] = sys_get_temp_dir();

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

        $command = new GenerateProxiesCommand($registry);
        $command->setLaravel($this->container);
        $input = new ArrayInput(['--em' => 'sqlite']);
        $output = new BufferedOutput();

        $command->run($input, $output);

        $this->assertEquals(
            $this->expected,
            $output->fetch()
        );
    }
}
