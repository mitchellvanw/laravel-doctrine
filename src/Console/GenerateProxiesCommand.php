<?php  namespace Mitch\LaravelDoctrine\Console;

use Illuminate\Console\Command;
use Doctrine\Common\Persistence\ManagerRegistry;

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

    /**
     * The ManagerRegistry
     *
     * @var \Doctrine\Common\Persistence\ManagerRegistry
     */
    private $registry;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct();

        $this->registry = $registry;
    }

    public function fire()
    {
        $this->info('Starting proxy generation....');
        foreach ($this->registry->getManagerNames() as $key => $value) {
            $manager = $this->registry->getManager($key);
            $metadata = $manager->getMetadataFactory()->getAllMetadata();
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
            $manager->getProxyFactory()->generateProxyClasses($metadata, $directory);
            $this->info('Proxies have been created.');
        }
    }
}
