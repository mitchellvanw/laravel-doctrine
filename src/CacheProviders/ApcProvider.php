<?php namespace Mitch\LaravelDoctrine\CacheProviders; 

use Doctrine\Common\Cache\ApcCache;

class ApcProvider implements Provider
{
    public function provide($config = null)
    {
        return new ApcCache;
    }
} 
