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
     * Execute the console command.
     *
     * @return void
     */
    public function fire(SchemaTool $tool, ClassMetadataFactory $metadata)
    {
        if ($this->option('sql')) {
            $this->info('Outputting create query:'.PHP_EOL);
            $sql = $tool->getCreateSchemaSql($metadata->getAllMetadata());
            $this->info(implode(';'.PHP_EOL, $sql));
        } else {
            $this->info('Creating database schema...');
            $tool->createSchema($metadata->getAllMetadata());
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
