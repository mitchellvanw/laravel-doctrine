<?php namespace Mitch\LaravelDoctrine\Console;

use InvalidArgumentException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class CacheClearCollectionRegionCommand extends Command {

    protected $name = 'doctrine:cache:clear-collection-region';
    protected $description = 'Clear a second-level cache collection region.';

    public function fire() {
        $entityManager = $this->laravel->make('Doctrine\ORM\EntityManagerInterface');
        $ownerClass = $this->argument('owner-class');
        $assoc = $this->argument('association');
        $ownerId = $this->argument('owner-id');
        $cache = $entityManager->getCache();
        if ( ! $cache instanceof Cache)
            throw new InvalidArgumentException('No second-level cache is configured on the given EntityManager.');
        if (( ! $ownerClass || ! $assoc) && ! $this->option('all'))
            throw new InvalidArgumentException('Missing arguments "--owner-class" "--association"');
        if ($this->option('flush')) {
            $collectionRegion = $cache->getCollectionCacheRegion($ownerClass, $assoc);
            if ( ! $collectionRegion instanceof DefaultRegion)
                throw new InvalidArgumentException(sprintf(
                    'The option "--flush" expects a "Doctrine\ORM\Cache\Region\DefaultRegion", but got "%s".',
                    is_object($collectionRegion) ? get_class($collectionRegion) : gettype($collectionRegion)
                ));
            $collectionRegion->getCache()->flushAll();
            $this->info("Flushing cache provider configured for <comment>{$ownerClass}#{$assoc}</comment>");
            return;
        }
        if ($this->option('all')) {
            $this->info('Clearing <comment>all</comment> second-level cache collection regions');
            $cache->evictEntityRegions();
            return;
        }
        if ($ownerId) {
            $this->info("Clearing second-level cache entry for collection <comment>{$ownerClass}#{$assoc}</comment> owner entity identified by <comment>{$ownerId}</comment>");
            $cache->evictCollection($ownerClass, $assoc, $ownerId);
            return;
        }
        $this->info("Clearing second-level cache for collection <comment>{$ownerClass}#{$assoc}</comment>");
        $cache->evictCollectionRegion($ownerClass, $assoc);
    }

    protected function getArguments() {
        return [
            ['owner-class', null, InputArgument::OPTIONAL, 'The owner entity name.'],
            ['association', null, InputArgument::OPTIONAL, 'The association collection name.'],
            ['owner-id', null, InputArgument::OPTIONAL, 'The owner identifier.'],
        ];
    }

    protected function getOptions() {
        return [
            ['all', null, InputOption::VALUE_NONE, 'If defined, all entity regions will be deleted/invalidated.'],
            ['flush', null, InputOption::VALUE_NONE, 'If defined, all cache entries will be flushed.'],
        ];
    }
} 
