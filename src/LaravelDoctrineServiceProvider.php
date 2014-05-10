<?php namespace Mitch\LaravelDoctrine;

use Doctrine\ORM\Events;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;
use Doctrine\Common\EventManager;
use Illuminate\Support\ServiceProvider;
use Mitch\LaravelDoctrine\CacheProviders;
use Mitch\LaravelDoctrine\EventListeners\SoftDeletableListener;

class LaravelDoctrineServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    public function boot()
    {
        $this->package('mitch/laravel-doctrine', 'doctrine', __DIR__.'/..');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerCacheManager();
        $this->registerEntityManager();
        $this->registerClassMetadataFactory();
        $this->registerDoctrineUserProvider();

        $this->commands([
            'Mitch\LaravelDoctrine\Console\SchemaCreateCommand',
            'Mitch\LaravelDoctrine\Console\SchemaUpdateCommand',
            'Mitch\LaravelDoctrine\Console\SchemaDropCommand'
        ]);
    }

    public function registerCacheManager()
    {
        $this->app->bind('Mitch\LaravelDoctrine\CacheManager', function($app) {
            $manager = new CacheManager($app['config']['doctrine::doctrine.cache']);
            $manager->add(new CacheProviders\ApcProvider);
            $manager->add(new CacheProviders\MemcacheProvider);
            $manager->add(new CacheProviders\RedisProvider);
            $manager->add(new CacheProviders\XcacheProvider);
            $manager->add(new CacheProviders\NullProvider);
            return $manager;
        });
    }

    private function registerEntityManager()
    {
        $this->app->singleton('Doctrine\ORM\EntityManager', function($app) {
            $config = $app['config']['doctrine::doctrine'];
            $metadata = Setup::createAnnotationMetadataConfiguration(
                $config['metadata'],
                $app['config']['app.debug'],
                $config['proxy']['directory'],
                $app['Mitch\LaravelDoctrine\CacheManager']->getCache($config['cache_provider']),
                $config['simple_annotations']
            );
            $metadata->addFilter('trashed', 'Mitch\LaravelDoctrine\Filters\TrashedFilter');
            $metadata->setAutoGenerateProxyClasses($config['proxy']['auto_generate']);
            $metadata->setDefaultRepositoryClassName($config['repository']);
            $metadata->setSQLLogger($config['logger']);

            if (isset($config['proxy']['namespace'])) {
                $metadata->setProxyNamespace($config['proxy']['namespace']);
            }
            $eventManager = new EventManager;
            $eventManager->addEventListener(Events::onFlush, new SoftDeletableListener);
            $entityManager = EntityManager::create($this->getDatabaseConfig($app['config']), $metadata, $eventManager);
            $entityManager->getFilters()->enable('trashed');
            return $entityManager;
        });
        $this->app->singleton('Doctrine\ORM\EntityManagerInterface', 'Doctrine\ORM\EntityManager');
    }

    private function registerClassMetadataFactory()
    {
        $this->app->singleton('Doctrine\ORM\Mapping\ClassMetadataFactory', function($app) {
            return $app['Doctrine\ORM\EntityManager']->getMetadataFactory();
        });
    }

    private function registerDoctrineUserProvider()
    {
        $this->app['auth']->extend('doctrine', function($app) {
            return new DoctrineUserProvider(
                $app['Illuminate\Hashing\HasherInterface'],
                $app['Doctrine\ORM\EntityManager'],
                $app['config']['auth.model']
            );
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [
            'Doctrine\ORM\EntityManagerInterface',
            'Doctrine\ORM\EntityManager',
            'Doctrine\ORM\Mapping\ClassMetadataFactory',
        ];
    }

    /**
     * Map Laravel's to Doctrine's database config
     *
     * @param  $config
     * @return array
     */
    private function getDatabaseConfig($config)
    {
        $default = $config['database.default'];
        $database = $config["database.connections.{$default}"];
        return [
            'driver'   => 'pdo_mysql',
            'host'     => $database['host'],
            'dbname'   => $database['database'],
            'user'     => $database['username'],
            'password' => $database['password'],
            'prefix'   => $database['prefix'],
        ];
    }
}
