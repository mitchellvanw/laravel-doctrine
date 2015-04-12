<?php namespace Mitch\LaravelDoctrine\Cache;

use Doctrine\Common\Cache\MemcachedCache;
use Memcached;

class MemcachedProvider implements Provider {

    public function make($config = null) {
        if ( ! extension_loaded('memcached'))
            throw new \RuntimeException('Memcached extension was not loaded.');

        $memcached = new Memcached;
        $memcached->addServer($config['host'], $config['port']);

        $cache = new MemcachedCache;
        $cache->setMemcached($memcached);
        return $cache;
    }

    public function isAppropriate($provider) {
        return $provider == 'memcached';
    }
}
