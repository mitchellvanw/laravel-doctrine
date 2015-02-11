<?php namespace Mitch\LaravelDoctrine\Console;

use Illuminate\Console\Command;
use InvalidArgumentException;
use Symfony\Component\Console\Input\InputArgument;
use Doctrine\ORM\Tools\Console\MetadataFilter;
use Symfony\Component\Console\Input\InputOption;

class GenerateProxiesCommand extends Command {

    protected $name = 'doctrine:generate:proxies';
    protected $description = 'Generate proxies for entities.';

    public function fire() {
        $entityManager = $this->laravel->make('Doctrine\ORM\EntityManagerInterface');
        $allMetadata = $entityManager->getMetadataFactory()->getAllMetadata();
        $allMetadata = MetadataFilter::filter($allMetadata, $this->option('filter'));
        $proxyDir = $entityManager->getConfiguration()->getProxyDir();
        // Process destination directory
        $destinationPath = $this->argument('destination') ? realpath($this->argument('destination')) : $proxyDir;
        if ( ! is_dir($destinationPath))
            mkdir($destinationPath, 0777, true);
        if ( ! file_exists($destinationPath))
            throw new InvalidArgumentException("Proxies destination directory <comment>{$destinationPath}</comment> does not exist.");
        if ( ! is_writable($destinationPath))
            throw new InvalidArgumentException("Proxies destination directory <comment>{$destinationPath}</comment> does not have write permissions.");
        if ( ! count($allMetadata)) {
            $this->info('No metadata classes to process.');
            return;
        }
        $this->info('Processing entities:');
        foreach ($allMetadata as $metadata) {
            $this->info("- {$metadata->name}");
        }
        // Generating Proxies
        $entityManager->getProxyFactory()->generateProxyClasses($allMetadata, $destinationPath);
        // Outputting information message
        $this->line('');
        $this->info("Proxies generated to <comment>{$destinationPath}</comment>.");
    }

    protected function getArguments() {
        return [
            ['destination', null, InputArgument::OPTIONAL, 'The path to generate your proxy classes. If none is provided, it will attempt to grab from configuration.']
        ];
    }

    protected function getOptions() {
        return [
            ['filter', null, InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'A string pattern used to match entities that should be processed.'],
        ];
    }
} 
