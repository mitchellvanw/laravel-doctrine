<?php namespace Mitch\LaravelDoctrine; 

use Mitch\LaravelDoctrine\CacheProviders\Provider;

class CacheManager
{
    private $providers = [];

    public function __construct($config)
    {
        $this->config = $config;
    }

    public function getCache($type)
    {
        foreach ($this->providers as $provider)
            if ($provider->isAppropriate($type))
                return $provider->make($this->getConfig($type));

        return null;
    }

    private function getConfig($provider)
    {
        return isset($this->config[$provider]) ? $this->config[$provider] : null;
    }

    public function add(Provider $provider)
    {
        $this->providers[] = $provider;
    }
} 
