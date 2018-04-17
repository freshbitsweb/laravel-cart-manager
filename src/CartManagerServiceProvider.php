<?php

namespace Freshbitsweb\LaravelCartManager;

use Freshbitsweb\LaravelCartManager\Contracts\CartDriver;
use Freshbitsweb\LaravelCartManager\Core\Cart;
use Freshbitsweb\LaravelCartManager\Observers\CartObserver;
use Freshbitsweb\LaravelCartManager\Middlewares\AttachCartCookie;
use Freshbitsweb\LaravelCartManager\Models\Cart as CartModel;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Support\ServiceProvider;

class CartManagerServiceProvider extends ServiceProvider
{
    /**
    * Publishes configuration file and registers error handler for Slack notification
    *
    * @return void
    */
    public function boot()
    {
        // Publish config file
        $this->publishes([
            __DIR__.'/../config/cart_manager.php' => config_path('cart_manager.php'),
        ], 'cart-manager-config');

        $this->publishes([
            __DIR__.'/../migrations/' => database_path('migrations')
        ], 'cart-manager-migrations');

        CartModel::observe(CartObserver::class);
    }

    /**
    * Service container bindings
    *
    * @return void
    */
    public function register()
    {
        // Users can specify only the options they actually want to override
        $this->mergeConfigFrom(
            __DIR__.'/../config/cart_manager.php', 'cart_manager'
        );

        // Bind the driver with contract
        $this->app->bind(CartDriver::class, $this->app['config']['cart_manager']['driver']);

        // Bind the cart class
        $this->app->bind(Cart::class, function($app) {
            return new Cart($app->make(CartDriver::class));
        });
    }
}
