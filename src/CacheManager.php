<?php namespace Mitch\LaravelDoctrine; 

class CacheManager
{
    private $provider;
    private $config;

    public function __construct($provider, $config)
    {
        $this->provider = $provider;
        $this->config = $config;
    }

    public function getCache()
    {
        return $this->provider ? $this->getCacheProvider($this->provider)->provide($this->getCacheConfig($this->provider)) : null;
    }

    private function getCacheProvider($provider)
    {
        $provider = ucfirst($provider);
        $class = $this->getFullClassName("{$provider}Provider");
        return new $class;
    }

    private function getFullClassName($class)
    {
        return "Mitch\\LaravelDoctrine\\CacheProviders\\{$class}";
    }

    private function getCacheConfig($provider)
    {
        return $this->config[$provider];
    }
} 
