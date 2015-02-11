<?php namespace Mitch\LaravelDoctrine\Console;

use InvalidArgumentException;
use LogicException;
use Symfony\Component\Console\Input\InputOption;
use Doctrine\Common\Cache\ApcCache;
use Doctrine\Common\Cache\XcacheCache;
use Illuminate\Console\Command;

class CacheClearResultCommand extends Command {

    protected $name = 'doctrine:cache:clear-result';
    protected $description = 'Clear all metadata cache of the various cache drivers.';

    public function fire() {
        $entityManager = $this->laravel->make('Doctrine\ORM\EntityManagerInterface');
        $cacheDriver = $entityManager->getConfiguration()->getResultCacheImpl();
        if ( ! $cacheDriver)
            throw new InvalidArgumentException('No Metadata cache driver is configured on given EntityManager.');
        if ($cacheDriver instanceof ApcCache)
            throw new LogicException("Cannot clear APC Cache from Console, its shared in the Webserver memory and not accessible from the CLI.");
        if ($cacheDriver instanceof XcacheCache)
            throw new LogicException("Cannot clear XCache Cache from Console, its shared in the Webserver memory and not accessible from the CLI.");
        $this->info('Clearing all metadata cache entries...');
        $result  = $cacheDriver->deleteAll();
        $message = ($result) ? 'Successfully deleted cache entries.' : 'No cache entries were deleted.';
        if ($this->option('flush')) {
            $result  = $cacheDriver->flushAll();
            $message = ($result) ? 'Successfully flushed cache entries.' : $message;
        }
        $this->info($message);
    }

    protected function getOptions() {
        return [
            ['flush', null, InputOption::VALUE_NONE, 'If defined, cache entries will be flushed instead of deleted/invalidated.'],
        ];
    }
} 
