<?php

namespace Freshbitsweb\LaravelCartManager\Test;

use Freshbitsweb\LaravelCartManager\Test\Support\TestProduct;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    use RefreshDatabase;

    /**
     * Setup the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        $this->loadMigrationsFrom(__DIR__.'/Support/database/migrations');
        $this->withFactories(__DIR__.'/Support/database/factories');

        // As tests are not http requests and do not read cookies
        cart()->setUser(1);
    }

    /**
     * @param  \Illuminate\Foundation\Application  $app
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return ['Freshbitsweb\LaravelCartManager\CartManagerServiceProvider'];
    }

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'sqlite');
        $app['config']->set('database.connections.sqlite', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
    }

    /**
     * Adds an item to the cart.
     *
     * @param int Quantity of the item
     * @param array Attributes to override
     * @return array Cart data
     */
    protected function addACartItem($quantity = 1, $attributes = [])
    {
        $testProduct = factory(TestProduct::class)->create($attributes);

        return TestProduct::addToCart($testProduct->id, $quantity);
    }
}
