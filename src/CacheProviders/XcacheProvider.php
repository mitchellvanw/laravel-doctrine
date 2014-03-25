<?php namespace Mitch\LaravelDoctrine\CacheProviders; 

use Doctrine\Common\Cache\XcacheCache;

class XcacheProvider implements Provider
{
    public function provider($config = null)
    {
        return new XcacheCache;
    }
} 
