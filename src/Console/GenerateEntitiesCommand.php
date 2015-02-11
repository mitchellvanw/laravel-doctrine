<?php namespace Mitch\LaravelDoctrine\Console;

use Illuminate\Console\Command;
use InvalidArgumentException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Doctrine\ORM\Tools\Console\MetadataFilter;
use Doctrine\ORM\Tools\EntityGenerator;
use Doctrine\ORM\Tools\DisconnectedClassMetadataFactory;

class GenerateEntitiesCommand extends Command {

    protected $name = 'doctrine:generate:entities';
    protected $description = 'Generate entity classes and method stubs from your mapping information.';

    public function fire() {
        $entityManager = $this->laravel->make('Doctrine\ORM\EntityManagerInterface');

        $metadataFactory = new DisconnectedClassMetadataFactory;
        $metadataFactory->setEntityManager($entityManager);
        $allMetadata = $metadataFactory->getAllMetadata();
        $allMetadata = MetadataFilter::filter($allMetadata, $this->option('filter'));
        $destination = $this->argument('destination');
        // Process destination directory
        $destinationPath = realpath($destination);
        if ( ! file_exists($destinationPath))
            throw new InvalidArgumentException("Entities destination directory <info>{$destinationPath}</info> does not exist.");
        if ( ! is_writable($destinationPath))
            throw new InvalidArgumentException("Entities destination directory <info>{$destinationPath}</info> does not have write permissions.");
        if ( ! count($allMetadata)) {
            $this->info('No metadata classes to process.');
            return;
        }
        $entityGenerator = $this->createEntityGenerator();
        $this->info('Processing entities:');
        foreach ($allMetadata as $metadata) {
            $this->info("- {$metadata->name}");
        }
        $entityGenerator->generate($allMetadata, $destinationPath);
        $this->line('');
        $this->info("Entities generated to <comment>{$destinationPath}</comment>.");
    }

    protected function getArguments() {
        return [
            ['destination', null, InputArgument::REQUIRED, 'The path to generate your entity classes.']
        ];
    }

    protected function getOptions() {
        return [
            ['filter', null, InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'A string pattern used to match entities that should be processed.'],
            ['generate-annotations', null, InputOption::VALUE_OPTIONAL, 'Flag to define if generator should generate annotation metadata on entities.', false],
            ['generate-methods', null, InputOption::VALUE_OPTIONAL, 'Flag to define if generator should generate stub methods on entities.', true],
            ['regenerate-entities', null, InputOption::VALUE_OPTIONAL, 'Flag to define if generator should regenerate entity if it exists.', false],
            ['update-entities', null, InputOption::VALUE_OPTIONAL, 'Flag to define if generator should only update entity if it exists.', true],
            ['extend', null, InputOption::VALUE_REQUIRED, 'Defines a base class to be extended by generated entity classes.'],
            ['spaces', null, InputOption::VALUE_REQUIRED, 'Defines the number of indentation spaces.', 4],
            ['no-backup', null, InputOption::VALUE_NONE, 'Flag to define if generator should avoid backing up existing entity file if it exists.'],
        ];
    }

    /**
     * @return EntityGenerator
     */
    private function createEntityGenerator() {
        $entityGenerator = new EntityGenerator;
        $entityGenerator->setGenerateAnnotations($this->option('generate-annotations'));
        $entityGenerator->setGenerateStubMethods($this->option('generate-methods'));
        $entityGenerator->setRegenerateEntityIfExists($this->option('regenerate-entities'));
        $entityGenerator->setUpdateEntityIfExists($this->option('update-entities'));
        $entityGenerator->setNumSpaces($this->option('spaces'));
        $entityGenerator->setBackupExisting(! $this->option('no-backup'));
        $extend = $this->option('extend');
        if ($extend)
            $entityGenerator->setClassToExtend($extend);
        return $entityGenerator;
    }
} 
