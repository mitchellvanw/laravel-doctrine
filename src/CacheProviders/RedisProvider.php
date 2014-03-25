<?php namespace Mitch\LaravelDoctrine\CacheProviders; 

use Doctrine\Common\Cache\RedisCache;
use Redis;

class RedisProvider implements Provider
{
    public function provide($config = null)
    {
        $redis = new Redis();
        $redis->connect($config['host'], $config['port']);

        if (isset($config['database'])) {
            $redis->select($config['database']);
        }

        $cache = new RedisCache();
        $cache->setRedis($redis);
        return $cache;
    }
} 
