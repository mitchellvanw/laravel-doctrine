<?php namespace Mitch\LaravelDoctrine\Console;

use Illuminate\Console\Command;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\Console\Input\InputOption;

class SchemaCreateCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'doctrine:schema:create';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create database schema from models';

    /**
     * The ManagerRegistry
     *
     * @var \Doctrine\Common\Persistence\ManagerRegistry
     */
    private $registry;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct();

        $this->registry = $registry;
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function fire()
    {
        if ($this->option('em')) {
            $manager = $this->registry->getManager($this->option('em'));
        } else {
            $manager = $this->registry->getManager();
        }

        $tool = new SchemaTool($manager);

        if ($this->option('sql')) {
            $this->info('Outputting create query:'.PHP_EOL);
            $sql = $tool->getCreateSchemaSql($manager->getMetadataFactory()->getAllMetadata());
            $this->info(implode(';'.PHP_EOL, $sql));
        } else {
            $this->info('Creating database schema...');
            $tool->createSchema($manager->getMetadataFactory()->getAllMetadata());
            $this->info('Schema has been created!');
        }
    }

    protected function getOptions()
    {
        return [
            ['sql', false, InputOption::VALUE_NONE, 'Dumps SQL query and does not execute creation.'],
            ['em', false, InputOption::VALUE_REQUIRED, 'Sets the entity manager when the default is not desired.'],
        ];
    }
}
