<?php

namespace TheCodingMachine\TDBM\Laravel\Providers;

use Doctrine\Common\Cache\Cache;
use Illuminate\Support\ServiceProvider;
use Psr\Log\LoggerInterface;
use TheCodingMachine\TDBM\Commands\GenerateCommand;
use TheCodingMachine\TDBM\Configuration;
use TheCodingMachine\TDBM\ConfigurationInterface;
use TheCodingMachine\TDBM\Laravel\Cache\IlluminateCacheAdapter;
use TheCodingMachine\TDBM\TDBMService;
use TheCodingMachine\TDBM\Utils\DefaultNamingStrategy;
use TheCodingMachine\TDBM\Utils\NamingStrategyInterface;

class TdbmServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {

    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        // Doctrine Cache setup for TDBM: link to Laravel cache
        $this->app->bind(Cache::class, IlluminateCacheAdapter::class);

        $this->app->bind(NamingStrategyInterface::class, DefaultNamingStrategy::class);

        $this->app->bind(ConfigurationInterface::class, Configuration::class);

        $this->app->singleton(Configuration::class, function ($app) {
            $daoNamespace = config('database.tdbm.daoNamespace', 'App\\Daos');
            $beanNamespace = config('database.tdbm.beanNamespace', 'App\\Beans');

            $db = $app->make('db');

            return new Configuration($beanNamespace, $daoNamespace, $db->connection()->getDoctrineConnection(), $app->make(NamingStrategyInterface::class), $app->make(Cache::class), null, $app->make(LoggerInterface::class));
        });

        $this->app->singleton(TDBMService::class, function ($app) {
            return new TDBMService($app->make(Configuration::class));
        });

        $this->commands(
            GenerateCommand::class
        );

        $this->mergeConfigFrom(__DIR__.'/../../config/tdbm.php', 'database');
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [
            Cache::class,
            NamingStrategyInterface::class,
            ConfigurationInterface::class,
            Configuration::class,
            TDBMService::class,
        ];
    }
}
