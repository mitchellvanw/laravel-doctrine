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
     * The schema tool.
     *
     * @var \Doctrine\ORM\Tools\SchemaTool
     */
    private $tool;

    /**
      * The ManagerRegistry
      *
      * @var \Doctrine\Common\Persistence\ManagerRegistry
      */
    private $registry;

    public function __construct(SchemaTool $tool, ManagerRegistry $registry)
    {
        parent::__construct();

        $this->tool = $tool;
        $this->registry = $registry;
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function fire()
    {
        foreach ($this->registry->getManagerNames() as $key => $value) {
            $manager = $this->registry->getManager($key);
            $sql = $this->tool->getDropSchemaSQL($manager->getMetadataFactory()->getAllMetadata());
            if (empty($sql)) {
                $this->info('Current models do not exist in schema.');
                continue;
            }
            if ($this->option('sql')) {
                $this->info('Outputting drop query:');
                $this->info(implode(';' . PHP_EOL, $sql));
            } else {
                $this->info('Dropping database schema....');
                $this->tool->dropSchema($manager->getMetadataFactory()->getAllMetadata());
                $this->info('Schema has been dropped!');
            }
        }
    }

    protected function getOptions()
    {
        return [
            ['sql', false, InputOption::VALUE_NONE, 'Dumps SQL query and does not execute drop.'],
        ];
    }
}
