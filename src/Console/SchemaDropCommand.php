<?php namespace Mitch\LaravelDoctrine\Console;

use Illuminate\Console\Command;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\Console\Input\InputOption;

class SchemaDropCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'doctrine:schema:drop';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Drop database schema';

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

        $sql = $tool->getDropSchemaSQL($manager->getMetadataFactory()->getAllMetadata());
        if (empty($sql)) {
            $this->info('Current models do not exist in schema.');
            exit;
        }
        if ($this->option('sql')) {
            $this->info('Outputting drop query:');
            $sql = $tool->getDropSchemaSQL($manager->getMetadataFactory()->getAllMetadata());
            $this->info(implode(';' . PHP_EOL, $sql));
        } else {
            $this->info('Dropping database schema....');
            $tool->dropSchema($manager->getMetadataFactory()->getAllMetadata());
            $this->info('Schema has been dropped!');
        }
    }

    protected function getOptions()
    {
        return [
            ['sql', false, InputOption::VALUE_NONE, 'Dumps SQL query and does not execute drop.'],
            ['em', false, InputOption::VALUE_REQUIRED, 'Sets the entity manager when the default is not desired.'],
        ];
    }
}
