<?php namespace Mitch\LaravelDoctrine\Console;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;

class SchemaDropCommand extends Command {

    protected $name = 'doctrine:schema:drop';
    protected $description = 'Drop database schema';

    public function fire() {
        $tool = $this->laravel->make('Doctrine\ORM\Tools\SchemaTool');
        $metadata = $this->laravel->make('Doctrine\ORM\Mapping\ClassMetadataFactory');

        $sql = $tool->getDropSchemaSQL($metadata->getAllMetadata());
        if (empty($sql)) {
            $this->info('Current entities do not exist in schema.');
            return;
        }
        if ($this->option('sql')) {
            $this->info('Outputting drop query:');
            $this->info(implode(';' . PHP_EOL, $sql));
        } else {
            $this->info('Dropping database schema....');
            $tool->dropSchema($metadata->getAllMetadata());
            $this->info('Schema has been dropped!');
        }
    }

    protected function getOptions() {
        return [
            ['sql', false, InputOption::VALUE_NONE, 'Dumps SQL query and does not execute drop.'],
        ];
    }
}

