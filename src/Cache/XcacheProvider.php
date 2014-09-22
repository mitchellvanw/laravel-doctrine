<?php namespace Mitch\LaravelDoctrine\Cache;

use Doctrine\Common\Cache\XcacheCache;

class XcacheProvider implements Provider
{
    public function make($config = null)
    {
        if ( ! extension_loaded('xcache'))
            throw new \RuntimeException('Xcache extension was not loaded.');

        return new XcacheCache;
    }

    public function isAppropriate($provider)
    {
        return $provider == 'xcache';
    }
}
