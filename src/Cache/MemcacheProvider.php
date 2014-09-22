<?php namespace Mitch\LaravelDoctrine\Cache;

use Doctrine\Common\Cache\MemcacheCache;
use Memcache;

class MemcacheProvider implements Provider
{
    public function make($config = null)
    {
        if ( ! extension_loaded('memcache'))
            throw new \RuntimeException('Memcache extension was not loaded.');

        $memcache = new Memcache;
        $memcache->connect($config['host'], $config['port']);

        $cache = new MemcacheCache;
        $cache->setMemcache($memcache);
        return $cache;
    }

    public function isAppropriate($provider)
    {
        return $provider == 'memcache';
    }
}
