<?php

namespace TheCodingMachine\TDBM\Laravel\Providers;


use Orchestra\Testbench\TestCase;
use TheCodingMachine\TDBM\TDBMService;

class TdbmServiceProviderTest extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [TdbmServiceProvider::class];
    }

    public function testServiceProvider()
    {
        $tdbmService = $this->app->make(TDBMService::class);
        $this->assertInstanceOf(TDBMService::class, $tdbmService);
    }
}
