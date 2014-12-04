<?php namespace Mitch\LaravelDoctrine\Console;

use Illuminate\Console\Command;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\Mapping\ClassMetadataFactory;
use Symfony\Component\Console\Input\InputOption;

class SchemaCreateCommand extends Command {

    protected $name = 'doctrine:schema:create';
    protected $description = 'Create database schema from models';

    /**
     * @var SchemaTool
     */
    private $tool;
    /**
     * @var ClassMetadataFactory
     */
    private $metadata;

    public function __construct(SchemaTool $tool, ClassMetadataFactory $metadata) {
        parent::__construct();
        $this->tool = $tool;
        $this->metadata = $metadata;
    }

    public function fire() {
        if ($this->option('sql')) {
            $this->info('Outputting create query:' . PHP_EOL);
            $sql = $this->tool->getCreateSchemaSql($this->metadata->getAllMetadata());
            $this->info(implode(';' . PHP_EOL, $sql));
        } else {
            $this->info('Creating database schema...');
            $this->tool->createSchema($this->metadata->getAllMetadata());
            $this->info('Schema has been created!');
        }
    }

    protected function getOptions() {
        return [
            ['sql', false, InputOption::VALUE_NONE, 'Dumps SQL query and does not execute creation.']
        ];
    }
} 
