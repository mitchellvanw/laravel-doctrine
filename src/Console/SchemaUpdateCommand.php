<?php namespace Mitch\LaravelDoctrine\Console; 

use Illuminate\Console\Command;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\Mapping\ClassMetadataFactory;
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
     * Execute the console command.
     *
     * @return void
     */
    public function fire(SchemaTool $tool, ClassMetadataFactory $metadata)
    {
        $this->info('Checking if database needs updating....');
        $clean = $this->option('clean');
        $sql = $tool->getUpdateSchemaSql($metadata->getAllMetadata(), $clean);
        if (empty($sql)) {
            $this->info('No updates found.');
            return;
        }
        if ($this->option('sql')) {
            $this->info('Outputting update query:');
            $this->info(implode(';' . PHP_EOL, $sql));
        } else {
            $this->info('Updating database schema....');
            $tool->updateSchema($metadata->getAllMetadata());
            $this->info('Schema has been updated!');
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

