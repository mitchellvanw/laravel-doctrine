<?php namespace Mitch\LaravelDoctrine;

use Doctrine\ORM\Events;
use Doctrine\ORM\EntityManager;
use Doctrine\Common\EventManager;
use Doctrine\ORM\Tools\Setup;
use Illuminate\Support\ServiceProvider;
use Mitch\LaravelDoctrine\EventListeners\SoftDeletableListener;

class LaravelDoctrineServiceProvider extends ServiceProvider
{
	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
        $this->package('mitch/laravel-doctrine', 'doctrine', __DIR__.'/..');

        $this->registerEntityManager();
        $this->registerClassMetadataFactory();

        $this->commands([
            'Mitch\LaravelDoctrine\Console\SchemaCreateCommand',
            'Mitch\LaravelDoctrine\Console\SchemaUpdateCommand',
            'Mitch\LaravelDoctrine\Console\SchemaDropCommand'
        ]);
	}

    private function registerEntityManager()
    {
        $this->app->singleton('Doctrine\ORM\EntityManager', function($app) {
            $config = $app['config']['doctrine::doctrine'];
            $manager = new CacheManager($config['provider'], $config['cache']);
            $metadata = Setup::createAnnotationMetadataConfiguration(
                $config['metadata'],
                $app['config']['app.debug'],
                $config['proxy']['directory'],
                $manager->getCache()
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
            $entityManager = EntityManager::create($config['connection'], $metadata, $eventManager);
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
    private function mapConfig($config)
    {
        $doctrine = $config['doctrine::doctrine'];
        $doctrine['connection']['host'] = $config->get('database.connections.mysql.host');
        $doctrine['connection']['dbname'] = $config->get('database.connections.mysql.database');
        $doctrine['connection']['user'] = $config->get('database.connections.mysql.username');
        $doctrine['connection']['password'] = $config->get('database.connections.mysql.password');
        return $doctrine;
    }
}
