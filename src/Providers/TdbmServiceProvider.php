<?php

namespace TheCodingMachine\TDBM\Laravel\Providers;

use Doctrine\Common\Cache\ApcCache;
use Doctrine\Common\Cache\ApcuCache;
use Doctrine\Common\Cache\ArrayCache;
use Doctrine\Common\Cache\Cache;
use Doctrine\Common\Cache\PhpFileCache;
use Doctrine\DBAL\Connection;
use Illuminate\Support\ServiceProvider;
use Psr\Log\LoggerInterface;
use TheCodingMachine\TDBM\Commands\GenerateCommand;
use TheCodingMachine\TDBM\Configuration;
use TheCodingMachine\TDBM\ConfigurationInterface;
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
        // Doctrine Cache setup for TDBM
        $this->app->singleton(Cache::class, function ($app) {
            // If DEBUG mode is on, let's just use an ArrayCache.
            if (config('app.debug')) {
                $driver = new ArrayCache();
            } else {
                // If APC is available, let's use APC
                if (extension_loaded("apcu")) {
                    $driver = new ApcuCache();
                } else if (extension_loaded("apc")) {
                    $driver = new ApcCache();
                } else {
                    $driver = new PhpFileCache(sys_get_temp_dir().'/doctrinecache');
                }
            }
            $driver->setNamespace(config('app.key'));
            return $driver;
        });

        $this->app->bind(NamingStrategyInterface::class, DefaultNamingStrategy::class);

        $this->app->bind(ConfigurationInterface::class, Configuration::class);

        $this->app->singleton(Configuration::class, function ($app) {
            $daoNamespace = config('database.tdbm.daoNamespace', 'App\\Daos');
            $beanNamespace = config('database.tdbm.beanNamespace', 'App\\Beans');

            return new Configuration($beanNamespace, $daoNamespace, $app->make('doctrine_dbal_connection'), $app->make(NamingStrategyInterface::class), $app->make(Cache::class), null, $app->make(LoggerInterface::class));
        });

        $this->app->singleton(TDBMService::class, function ($app) {
            return new TDBMService($app->make(Configuration::class));
        });

        $this->commands(
            GenerateCommand::class
        );
    }
}
