<?php namespace Mitch\LaravelDoctrine\Console;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;

class SchemaCreateCommand extends Command {

    protected $name = 'doctrine:schema:create';
    protected $description = 'Create database schema from entities.';

    public function fire() {
        $tool = $this->laravel->make('Doctrine\ORM\Tools\SchemaTool');
        $metadata = $this->laravel->make('Doctrine\ORM\Mapping\ClassMetadataFactory');
        if ($this->option('sql')) {
            $this->info('Outputting create query:' . PHP_EOL);
            $sql = $tool->getCreateSchemaSql($metadata->getAllMetadata());
            $this->info(implode(';' . PHP_EOL, $sql));
        } else {
            $this->info('Creating database schema...');
            $tool->createSchema($metadata->getAllMetadata());
            $this->info('Schema has been created!');
        }
    }

    protected function getOptions() {
        return [
            ['sql', false, InputOption::VALUE_NONE, 'Dumps SQL query and does not execute creation.']
        ];
    }
} 
