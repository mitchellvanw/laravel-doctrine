<?php namespace Mitch\LaravelDoctrine\Console;

use Symfony\Component\Console\Input\InputOption;

class SchemaCreateCommand extends SchemaCommand
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
     * Execute the console command.
     *
     * @return void
     */
    public function fire()
    {
        if ($this->option('sql')) {
            $this->info('Outputting create query:');
            $sql = $this->getTool()->getCreateSchemaSql($this->metadata->getAllMetadata());
            $this->info(implode(';' . PHP_EOL, $sql));
        } else {
            $this->info('Creating database schema...');
            $this->getTool()->createSchema($this->metadata->getAllMetadata());
            $this->info('Schema has been created!');
        }
    }

    protected function getOptions()
    {
        return [
            ['sql', null, InputOption::VALUE_OPTIONAL, 'Dumps SQL query and does not execute creation.']
        ];
    }
} 
