<?php namespace Mitch\LaravelDoctrine\Console;

use Illuminate\Console\Command;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\Console\Input\InputOption;

class SchemaUpdateCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'doctrine:schema:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update database schema to match models';

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
        $this->info('Checking if database needs updating....');
        $clean = $this->option('clean');

        if ($this->option('em')) {
            $manager = $this->registry->getManager($this->option('em'));
        } else {
            $manager = $this->registry->getManager();
        }

        $tool = new SchemaTool($manager);

        $sql = $tool->getUpdateSchemaSql($manager->getMetadataFactory()->getAllMetadata(), $clean);

        if (empty($sql)) {
            $this->info('No updates found.');
            exit;
        }
        if ($this->option('sql')) {
            $this->info('Outputting update query:');
            $this->info(implode(';' . PHP_EOL, $sql));
        } else {
            $this->info('Updating database schema....');
            $tool->updateSchema($manager->getMetadataFactory()->getAllMetadata());
            $this->info('Schema has been updated!');
        }
    }

    protected function getOptions()
    {
        return [
            ['sql', false, InputOption::VALUE_NONE, 'Dumps SQL query and does not execute update.'],
            ['clean', null, InputOption::VALUE_OPTIONAL, 'When using clean model all non-relevant to this metadata assets will be cleared.'],
            ['em', false, InputOption::VALUE_REQUIRED, 'Sets the entity manager when the default is not desired.'],
        ];
    }
}
