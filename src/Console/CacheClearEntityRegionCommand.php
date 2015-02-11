<?php namespace Mitch\LaravelDoctrine\Console;

use InvalidArgumentException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Doctrine\ORM\Cache\Region\DefaultRegion;
use Doctrine\ORM\Cache;
use Illuminate\Console\Command;

class CacheClearEntityRegionCommand extends Command {

    protected $name = 'doctrine:cache:clear-entity-region';
    protected $description = 'Clear a second-level cache entity region.';

    public function fire() {
        $entityManager = $this->laravel->make('Doctrine\ORM\EntityManagerInterface');
        $entityClass = $this->argument('entity-class');
        $entityId = $this->argument('entity-id');
        $cache = $entityManager->getCache();
        if ( ! $cache instanceof Cache)
            throw new InvalidArgumentException('No second-level cache is configured on the given EntityManager.');
        if ( ! $entityClass && ! $this->option('all'))
            throw new InvalidArgumentException('Invalid argument "--entity-class"');
        if ($this->option('flush')) {
            $entityRegion = $cache->getEntityCacheRegion($entityClass);
            if ( ! $entityRegion instanceof DefaultRegion)
                throw new InvalidArgumentException(sprintf(
                    'The option "--flush" expects a "Doctrine\ORM\Cache\Region\DefaultRegion", but got "%s".',
                    is_object($entityRegion) ? get_class($entityRegion) : gettype($entityRegion)
                ));
            $entityRegion->getCache()->flushAll();
            $this->info("Flushing cache provider configured for entity named <comment>{$entityClass}</comment>.");
            return;
        }
        if ($this->option('all')) {
            $this->info('Clearing <comment>all</comment> second-level cache entity regions.');
            $cache->evictEntityRegions();
            return;
        }
        if ($entityId) {
            $this->info("Clearing second-level cache entry for entity <comment>{$entityClass}</comment> identified by <comment>{$entityId}</comment>.");
            $cache->evictEntity($entityClass, $entityId);
            return;
        }
        $this->info("Clearing second-level cache for entity <comment>{$entityClass}</comment>");
        $cache->evictEntityRegion($entityClass);
    }

    protected function getArguments() {
        return [
            ['entity-class', null, InputArgument::OPTIONAL, 'The entity name.'],
            ['entity-id', null, InputArgument::OPTIONAL, 'The entity identifier.'],
        ];
    }

    protected function getOptions() {
        return [
            ['all', null, InputOption::VALUE_NONE, 'If defined, all entity regions will be deleted/invalidated.'],
            ['flush', null, InputOption::VALUE_NONE, 'If defined, all cache entries will be flushed.'],
        ];
    }
} 
