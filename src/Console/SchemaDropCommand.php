<?php namespace Mitch\LaravelDoctrine\Console;

use Illuminate\Console\Command;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\Mapping\ClassMetadataFactory;
use Symfony\Component\Console\Input\InputOption;

class SchemaDropCommand extends Command {

    protected $name = 'doctrine:schema:drop';
    protected $description = 'Drop database schema';

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

    /**
     * Execute the console command.
     * @return void
     */
    public function fire() {
        $sql = $this->tool->getDropSchemaSQL($this->metadata->getAllMetadata());
        if (empty($sql)) {
            $this->info('Current models do not exist in schema.');
            return;
        }
        if ($this->option('sql')) {
            $this->info('Outputting drop query:');
            $this->info(implode(';' . PHP_EOL, $sql));
        } else {
            $this->info('Dropping database schema....');
            $this->tool->dropSchema($this->metadata->getAllMetadata());
            $this->info('Schema has been dropped!');
        }
    }

    protected function getOptions() {
        return [
            ['sql', false, InputOption::VALUE_NONE, 'Dumps SQL query and does not execute drop.'],
        ];
    }
}

