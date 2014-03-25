<?php namespace Mitch\LaravelDoctrine;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;
use Illuminate\Support\ServiceProvider;

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
            $metadata->setAutoGenerateProxyClasses($config['proxy']['auto_generate']);
            $metadata->setDefaultRepositoryClassName($config['repository']);
            $metadata->setSQLLogger($config['logger']);

            if (isset($config['proxy']['namespace'])) {
                $metadata->setProxyNamespace($config['proxy']['namespace']);
            }
            return EntityManager::create($config['connection'], $metadata);
        });
        $this->app->bind('Doctrine\ORM\EntityManagerInterface', 'Doctrine\ORM\EntityManager');
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['Doctrine\ORM\EntityManager'];
    }
}
