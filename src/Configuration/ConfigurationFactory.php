<?php namespace Mitch\LaravelDoctrine\Configuration;

use Doctrine\ORM\Cache;
use Doctrine\ORM\Configuration;
use Mitch\LaravelDoctrine\CacheManager;
use Illuminate\Contracts\Config\Repository;
use Mitch\LaravelDoctrine\Metadata\MetadataStrategyFactory;

class ConfigurationFactory
{
	private $metadataFactory;
    private $config;
    private $cacheManager;

    public function __construct(MetadataStrategyFactory $metadataFactory, Repository $config, CacheManager $cacheManager)
    {
        $this->metadataFactory = $metadataFactory;
        $this->config          = $config;
        $this->cacheManager    = $cacheManager;
    }

    /**
     * Reads the config files and builds a doctrine Configuration object.
     *
     * @return \Doctrine\ORM\Configuration
     */
    public function create()
    {
        $configuration = $this->createConfiguration(
            $this->config->get('app.debug'),
            $this->config->get('doctrine::proxy.directory')
        );

        if ($cacheProvider = $this->config->get('doctrine::cache_provider'))
        {
            $cache = $this->cacheManager->getCache($cacheProvider);

            $configuration->setMetadataCacheImpl($cache);
            $configuration->setQueryCacheImpl($cache);
            $configuration->setResultCacheImpl($cache);
        }

        $this->applyMetadata($configuration);

        return $configuration;
    }

    /**
     * @param bool $isDevMode
     * @param string|null $proxyDir
     *
     * @return \Doctrine\ORM\Configuration
     */
    private function createConfiguration($isDevMode = false, $proxyDir = null)
    {
        $proxyDir = $proxyDir ?: sys_get_temp_dir();

        $config = new Configuration();
        $config->setProxyDir($proxyDir);
        $config->setProxyNamespace('DoctrineProxies');
        $config->setAutoGenerateProxyClasses($isDevMode);

        return $config;
    }

    /**
     * @param \Doctrine\ORM\Configuration $configuration
     *
     * @return void
     */
    private function applyMetadata(Configuration $configuration)
    {
        foreach ($this->config->get('doctrine::doctrine.metadata.custom_drivers', []) as $type => $factory)
        {
            $this->metadataFactory->addCustomType($type, $factory);
        }

        $metadataStrategy = $this->metadataFactory->getStrategy(
            // Defaults to annotations for BC
            $this->config->get('doctrine::doctrine.mappings.driver', 'annotations'),
            // Defaults to the previous setup of paths + simple_annotations
            // The new params key allows the user to provide an array of arguments
            // That will be passed on to the driver constructor
            $this->config->get('doctrine::doctrine.mappings.params', [
                $this->config->get('doctrine::doctrine.metadata'),
                $this->config->get('doctrine::doctrine.simple_annotations')
            ])
        );

        $metadataStrategy->apply($configuration);
    }
}
