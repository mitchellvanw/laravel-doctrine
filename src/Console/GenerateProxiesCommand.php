<?php  namespace Mitch\LaravelDoctrine\Console;

use Illuminate\Console\Command;

class GenerateProxiesCommand extends Command {

    protected $name = 'doctrine:generate:proxies';
    protected $description = 'Generate proxies for entities.';

    public function fire() {
        $entityManager = $this->laravel->make('Doctrine\ORM\EntityManagerInterface');
        
        $this->info('Starting proxy generation....');
        $metadata = $entityManager->getMetadataFactory()->getAllMetadata();
        if (empty($metadata)) {
            $this->error('No metadata found to generate any entities.');
            exit;
        }
        $directory = $this->laravel['config']['doctrine::doctrine.proxy.directory'];
        if ( ! $directory) {
            $this->error('The proxy directory has not been set.');
            exit;
        }
        $this->info('Processing entities:');
        foreach ($metadata as $item)
            $this->line($item->name);
        $entityManager->getProxyFactory()->generateProxyClasses($metadata, $directory);
        $this->info('Proxies have been created.');
    }
} 
