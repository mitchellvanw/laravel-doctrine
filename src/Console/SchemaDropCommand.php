<?php namespace Mitch\LaravelDoctrine\Console; 

use Symfony\Component\Console\Input\InputOption;

class SchemaDropCommand extends SchemaCommand
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
     * Execute the console command.
     *
     * @return void
     */
    public function fire()
    {
        $sql = $this->getTool()->getDropSchemaSQL($this->metadata->getAllMetadata());
        if (empty($sql)) {
            $this->info('Current models do not exist in schema.');
            return;
        }
        if ($this->option('sql')) {
            $this->info('Outputting drop query:');
            $this->info(implode(';' . PHP_EOL, $sql));
        } else {
            $this->info('Dropping database schema....');
            $this->getTool()->dropSchema($this->metadata->getAllMetadata());
            $this->info('Schema has been dropped!');
        }
    }

    protected function getOptions()
    {
        return [
            ['sql', false, InputOption::VALUE_NONE, 'Dumps SQL query and does not execute drop.'],
        ];
    }
}

