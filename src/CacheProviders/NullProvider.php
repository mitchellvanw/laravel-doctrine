<?php namespace Mitch\LaravelDoctrine\CacheProviders;

class NullProvider implements Provider
{
    public function make($config = null)
    {
        return null;
    }

    public function isAppropriate($provider)
    {
        return $provider == null || $provider == 'null' || $provider == 'NULL';
    }
}
