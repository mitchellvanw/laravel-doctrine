<?php namespace Mitch\LaravelDoctrine\Console;

use InvalidArgumentException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Doctrine\ORM\Cache\Region\DefaultRegion;
use Doctrine\ORM\Cache;
use Illuminate\Console\Command;

class CacheClearQueryRegionCommand extends Command {

    protected $name = 'doctrine:cache:clear-query-region';
    protected $description = 'Clear a second-level cache entity region.';

    public function fire() {
        $entityManager = $this->laravel->make('Doctrine\ORM\EntityManagerInterface');
        $name = $this->argument('region-name');
        $cache = $entityManager->getCache();
        if ($name === null)
            $name = Cache::DEFAULT_QUERY_REGION_NAME;
        if ( ! $cache instanceof Cache)
            throw new InvalidArgumentException('No second-level cache is configured on the given EntityManager.');
        if ($this->option('flush')) {
            $queryCache = $cache->getQueryCache($name);
            $queryRegion = $queryCache->getRegion();
            if ( ! $queryRegion instanceof DefaultRegion)
                throw new InvalidArgumentException(sprintf(
                    'The option "--flush" expects a "Doctrine\ORM\Cache\Region\DefaultRegion", but got "%s".',
                    is_object($queryRegion) ? get_class($queryRegion) : gettype($queryRegion)
                ));
            $queryRegion->getCache()->flushAll();
            $this->info("Flushing cache provider configured for second-level cache query region named <comment>{$name}</comment>");
            return;
        }
        if ($this->option('all')) {
            $this->info('Clearing <comment>all</comment> second-level cache query regions');
            $cache->evictQueryRegions();
            return;
        }
        $this->info("Clearing second-level cache query region named <comment>{$name}</comment>");
        $cache->evictQueryRegion($name);
    }

    protected function getArguments() {
        return [
            ['region-name', null, InputArgument::OPTIONAL, 'The query region to clear.'],
        ];
    }

    protected function getOptions() {
        return [
            ['all', null, InputOption::VALUE_NONE, 'If defined, all entity regions will be deleted/invalidated.'],
            ['flush', null, InputOption::VALUE_NONE, 'If defined, all cache entries will be flushed.'],
        ];
    }
} 
