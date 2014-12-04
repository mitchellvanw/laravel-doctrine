<?php  namespace Mitch\LaravelDoctrine\Console;

use Illuminate\Console\Command;
use Doctrine\ORM\EntityManagerInterface;

class GenerateProxiesCommand extends Command {

    protected $name = 'doctrine:generate:proxies';
    protected $description = 'Generate proxies for entities.';

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager) {
        parent::__construct();
        $this->entityManager = $entityManager;
    }

    public function fire() {
        $this->info('Starting proxy generation....');
        $metadata = $this->entityManager->getMetadataFactory()->getAllMetadata();
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
        $this->entityManager->getProxyFactory()->generateProxyClasses($metadata, $directory);
        $this->info('Proxies have been created.');
    }
} 
