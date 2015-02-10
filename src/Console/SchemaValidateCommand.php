<?php namespace Mitch\LaravelDoctrine\Console;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;

class SchemaValidateCommand extends Command {

    protected $name = 'doctrine:schema:validate';
    protected $description = 'Validate the mapping files.';

    public function fire() {
        $entityManager = $this->laravel->make('Doctrine\ORM\EntityManagerInterface');
        $validator = new SchemaValidator($entityManager);
        $exit = 0;
        if ( ! $this->option('skip-mapping')) {
            $errors = $validator->validateMapping();
            if ($errors) {
                foreach ($errors as $class => $errorMessages) {
                    $this->error("[Mapping]  FAIL - The entity-class [{$class}] mapping is invalid:");
                    foreach ($errorMessages as $message)
                        $this->line("- {$message}");
                    $this->line(PHP_EOL);
                }
                $exit += 1;
            } else {
                $this->info('[Mapping]  OK - The mapping files are correct.');
            }
        } else {
            $this->comment('[Mapping]  Skipped mapping check.');
        }
        if ( ! $this->option('skip-sync')) {
            if ( ! $validator->schemaInSyncWithMetadata()) {
                $this->error('[Database] FAIL - The database schema is not in sync with the current mapping file.');
                $exit += 2;
            } else {
                $this->info('[Database] OK - The database schema is in sync with the mapping files.');
            }
        } else {
            $this->comment('[Database] SKIPPED - The database was not checked for synchronicity.');
        }
        return $exit;
    }

    protected function getOptions() {
        return [
            ['skip-mapping', false, InputOption::VALUE_NONE, 'Skip mapping validation check.'],
            ['skip-sync', false, InputOption::VALUE_NONE, 'Skip checking if the mapping is in sync with the database.']
        ];
    }
} 
