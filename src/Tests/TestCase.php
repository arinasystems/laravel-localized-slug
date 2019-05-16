<?php

namespace ArinaSystems\LocalizedSlug\Tests;

use Mockery;
use Illuminate\Contracts\Events\Dispatcher;
use Orchestra\Testbench\TestCase as Orchestra;
use ArinaSystems\LocalizedSlug\Providers\SluggerServiceProvider;
use ArinaSystems\LocalizedSlug\Providers\LocalizedSlugServiceProvider;
use ArinaSystems\LocalizedSlug\Tests\App\Providers\TestingServiceProvider;

/**
 * Class TestCase
 *
 * @package Tests
 */
abstract class TestCase extends Orchestra
{
    /**
     * Setup the test environment.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->withFactories(realpath(__DIR__ . '/database/factories'));

        $this->artisan('migrate', ['--database' => 'testing']);

        $this->beforeApplicationDestroyed(function () {
            $this->artisan('migrate:rollback');
        });
    }

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        // set up database configuration
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);
    }

    /**
     * Get LocalizedSlug package providers.
     *
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            TestingServiceProvider::class,
            LocalizedSlugServiceProvider::class,
            SluggerServiceProvider::class,
        ];
    }

    /**
     * @param  $app
     * @return array
     */
    protected function getPackageAliases($app)
    {
        return [
            'Slugger' => 'ArinaSystems\LocalizedSlug\Facades\Slugger',
        ];
    }
}
