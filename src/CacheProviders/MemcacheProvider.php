<?php namespace Mitch\LaravelDoctrine\CacheProviders; 

use Doctrine\Common\Cache\MemcacheCache;
use Memcache;

class MemcacheProvider implements Provider
{
    public function provide($config = null)
    {
        $memcache = new Memcache;
        $memcache->connect($config['host'], $config['port']);

        $cache = new MemcacheCache;
        $cache->setMemcache($memcache);
        return $cache;
    }
} 
