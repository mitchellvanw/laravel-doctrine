<?php namespace Mitch\LaravelDoctrine\Console;

use Illuminate\Console\Command;
use InvalidArgumentException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Doctrine\ORM\Tools\Console\MetadataFilter;
use Doctrine\ORM\Tools\EntityRepositoryGenerator;

class GenerateRepositoriesCommand extends Command {

    protected $name = 'doctrine:generate:repositories';
    protected $description = 'Generate repository classes from your mapping information.';

    public function fire() {
        $entityManager = $this->laravel->make('Doctrine\ORM\EntityManagerInterface');
        $allMetadata = $entityManager->getMetadataFactory()->getAllMetadata();
        $allMetadata = MetadataFilter::filter($allMetadata, $this->option('filter'));
        $repositoryName = $entityManager->getConfiguration()->getDefaultRepositoryClassName();
        // Process destination directory
        $destinationPath = realpath($this->argument('destination'));
        if ( ! file_exists($destinationPath))
            throw new InvalidArgumentException("Entities destination directory <comment>{$destinationPath}</comment> does not exist.");
        if ( ! is_writable($destinationPath))
            throw new InvalidArgumentException("Entities destination directory <info>{$destinationPath}</info> does not have write permissions.");
        if ( ! count($allMetadata)) {
            $this->info('No metadata classes to process.');
            return;
        }
        $numRepositories = 0;
        $generator = new EntityRepositoryGenerator;
        $generator->setDefaultRepositoryName($repositoryName);
        foreach ($allMetadata as $metadata) {
            if ( ! $metadata->customRepositoryClassName)
                continue;
            $this->info("Processing repository <comment>{$metadata->customRepositoryClassName}</comment>");
            $generator->writeEntityRepositoryClass($metadata->customRepositoryClassName, $destinationPath);
            $numRepositories++;
        }

        $this->line('');
        if ($numRepositories)
            $this->info("Repositories generated to <comment>{$destinationPath}</comment>");
        else
            $this->info('No repositories were found to be processed.');
    }

    protected function getArguments() {
        return [
            ['destination', null, InputArgument::REQUIRED, 'The path to generate your repository classes.']
        ];
    }

    protected function getOptions() {
        return [
            ['filter', null, InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'A string pattern used to match entities that should be processed.'],
        ];
    }
} 
