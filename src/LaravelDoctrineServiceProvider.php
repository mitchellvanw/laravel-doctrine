<?php namespace Mitch\LaravelDoctrine;

use App;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Events;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadataFactory;
use Doctrine\ORM\Tools\Setup;
use Doctrine\Common\EventManager;
use Illuminate\Auth\AuthManager;
use Illuminate\Support\ServiceProvider;
use Mitch\LaravelDoctrine\Cache;
use Mitch\LaravelDoctrine\Configuration\ConfigurationFactory;
use Mitch\LaravelDoctrine\Configuration\DriverMapper;
use Mitch\LaravelDoctrine\Configuration\SqlMapper;
use Mitch\LaravelDoctrine\Configuration\SqliteMapper;
use Mitch\LaravelDoctrine\Configuration\OCIMapper;
use Mitch\LaravelDoctrine\EventListeners\SoftDeletableListener;
use Mitch\LaravelDoctrine\Filters\TrashedFilter;
use Mitch\LaravelDoctrine\Validation\DoctrinePresenceVerifier;

class LaravelDoctrineServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     * @var bool
     */
    protected $defer = false;

    public function boot()
    {
        $this->package('mitchellvanw/laravel-doctrine', 'doctrine', __DIR__ . '/..');
        $this->extendAuthManager();
    }

    /**
     * Register the service provider.
     * @return void
     */
    public function register()
    {
        $this->registerConfigurationMapper();
        $this->registerCacheManager();
        $this->registerEntityManager();
        $this->registerClassMetadataFactory();
        $this->registerValidationVerifier();

        $this->commands([
            'Mitch\LaravelDoctrine\Console\GenerateProxiesCommand',
            'Mitch\LaravelDoctrine\Console\SchemaCreateCommand',
            'Mitch\LaravelDoctrine\Console\SchemaUpdateCommand',
            'Mitch\LaravelDoctrine\Console\SchemaDropCommand'
        ]);
    }

    /**
     * The driver mapper's instance needs to be accessible from anywhere in the application,
     * for registering new mapping configurations or other storage libraries.
     */
    private function registerConfigurationMapper()
    {
        $this->app->bind(DriverMapper::class, function () {
            $mapper = new DriverMapper;
            $mapper->registerMapper(new SqlMapper);
            $mapper->registerMapper(new SqliteMapper);
            $mapper->registerMapper(new OCIMapper);
            return $mapper;
        });
    }

    /**
     * Registers a new presence verifier for Laravel 4 validation. Specifically, this
     * is for the use of the Doctrine ORM.
     */
    public function registerValidationVerifier()
    {
        $this->app->bindShared('validation.presence', function()
        {
            return new DoctrinePresenceVerifier(EntityManagerInterface::class);
        });
    }

    public function registerCacheManager()
    {
        $this->app->bind(CacheManager::class, function ($app) {
            $manager = new CacheManager($app['config']['doctrine::doctrine.cache']);
            $manager->add(new Cache\ApcProvider);
            $manager->add(new Cache\MemcacheProvider);
            $manager->add(new Cache\RedisProvider);
            $manager->add(new Cache\XcacheProvider);
            $manager->add(new Cache\NullProvider);
            return $manager;
        });
    }

    private function registerEntityManager()
    {
        $this->app->singleton(EntityManager::class, function ($app) {
            $config = $app['config']['doctrine::doctrine'];

            /** @type ConfigurationFactory $configurationFactory */
            $configurationFactory = $app->make(ConfigurationFactory::class);

            $configuration = $configurationFactory->create();

            $configuration->addFilter('trashed', TrashedFilter::class);
            $configuration->setAutoGenerateProxyClasses($config['proxy']['auto_generate']);
            $configuration->setDefaultRepositoryClassName($config['repository']);
            $configuration->setSQLLogger($config['logger']);

            if (isset($config['proxy']['namespace']))
                $configuration->setProxyNamespace($config['proxy']['namespace']);

            $eventManager = new EventManager;
            $eventManager->addEventListener(Events::onFlush, new SoftDeletableListener);
            $entityManager = EntityManager::create($this->mapLaravelToDoctrineConfig($app['config']), $configuration, $eventManager);
            $entityManager->getFilters()->enable('trashed');
            return $entityManager;
        });

        $this->app->alias(EntityManager::class, EntityManagerInterface::class);
    }

    private function registerClassMetadataFactory()
    {
        $this->app->singleton(ClassMetadataFactory::class, function ($app) {
            return $app[EntityManager::class]->getMetadataFactory();
        });
    }

    private function extendAuthManager()
    {
        $this->app[AuthManager::class]->extend('doctrine', function ($app) {
            return new DoctrineUserProvider(
                $app['Illuminate\Hashing\HasherInterface'],
                $app[EntityManager::class],
                $app['config']['auth.model']
            );
        });
    }

   
    /**
     * Map Laravel's to Doctrine's database configuration requirements.
     * @param $config
     * @throws \Exception
     * @return array
     */
    private function mapLaravelToDoctrineConfig($config)
    {
        $default = $config['database.default'];
        $connection = $config["database.connections.{$default}"];
        return App::make(DriverMapper::class)->map($connection);
    }
}
