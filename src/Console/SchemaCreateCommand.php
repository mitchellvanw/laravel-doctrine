<?php namespace Mitch\LaravelDoctrine\Console;

use Illuminate\Console\Command;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\Mapping\ClassMetadataFactory;
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
     * The schema tool.
     *
     * @var \Doctrine\ORM\Tools\SchemaTool
     */
    private $tool;

    /**
     * The class metadata factory
     *
     * @var \Doctrine\ORM\Tools\SchemaTool
     */
    private $metadata;

    public function __construct(SchemaTool $tool, ClassMetadataFactory $metadata)
    {
        parent::__construct();

        $this->tool = $tool;
        $this->metadata = $metadata;
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function fire()
    {
        if ($this->option('sql')) {
            $this->info('Outputting create query:'.PHP_EOL);
            $sql = $this->tool->getCreateSchemaSql($this->metadata->getAllMetadata());
            $this->info(implode(';'.PHP_EOL, $sql));
        } else {
            $this->info('Creating database schema...');
            $this->tool->createSchema($this->metadata->getAllMetadata());
            $this->info('Schema has been created!');
        }
    }

    protected function getOptions()
    {
        return [
            ['sql', false, InputOption::VALUE_NONE, 'Dumps SQL query and does not execute creation.']
        ];
    }
} 
