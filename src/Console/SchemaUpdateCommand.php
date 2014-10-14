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
        $this->info('Checking if database needs updating....');
        $clean = $this->option('clean');
        foreach ($this->registry->getManagerNames() as $key => $value) {
            $manager = $this->registry->getManager($key);
            $sql = $this->tool->getUpdateSchemaSql($manager->getMetadataFactory()->getAllMetadata(), $clean);
            if (empty($sql)) {
                $this->info('No updates found.');
                continue;
            }
            if ($this->option('sql')) {
                $this->info('Outputting update query:');
                $this->info(implode(';' . PHP_EOL, $sql));
            } else {
                $this->info('Updating database schema....');
                $this->tool->updateSchema($this->metadata->getAllMetadata());
                $this->info('Schema has been updated!');
            }
        }
    }

    protected function getOptions()
    {
        return [
            ['sql', false, InputOption::VALUE_NONE, 'Dumps SQL query and does not execute update.'],
            ['clean', null, InputOption::VALUE_OPTIONAL, 'When using clean model all non-relevant to this metadata assets will be cleared.']
        ];
    }
}
