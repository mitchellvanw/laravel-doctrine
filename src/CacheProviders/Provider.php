<?php namespace Mitch\LaravelDoctrine\CacheProviders; 

interface Provider
{
    public function make($config = null);
    public function isAppropriate($provider);
} 
