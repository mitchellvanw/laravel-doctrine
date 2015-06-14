<?php  namespace Mitch\LaravelDoctrine\Console;

use Illuminate\Console\Command;
use Doctrine\ORM\EntityManagerInterface;

class GenerateProxiesCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'doctrine:generate:proxies';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate proxies for entities.';

    public function fire(EntityManagerInterface $entityManager)
    {
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
        foreach ($metadata as $item) {
            $this->line($item->name);
        }
        $entityManager->getProxyFactory()->generateProxyClasses($metadata, $directory);
        $this->info('Proxies have been created.');
    }
} 
