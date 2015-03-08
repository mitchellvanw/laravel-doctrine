<?php namespace Mitch\LaravelDoctrine\Cache;

use Doctrine\Common\Cache\ArrayCache;

class ArrayProvider implements Provider
{
    public function make($config = null)
    {
        return new ArrayCache();
    }

    public function isAppropriate($provider)
    {
        return $provider == 'array';
    }
}
