<?php

namespace Freshbitsweb\CartManager;

use Freshbitsweb\CartManager\Contracts\CartDriver;
use Freshbitsweb\CartManager\Core\Cart;
use Freshbitsweb\CartManager\Middlewares\AttachCartCookie;
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

        // Migration files path
        $this->loadMigrationsFrom(__DIR__.'/../migrations');
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

        // Attach the cart cookie to the user browser
        $httpKernel = $this->app->make(Kernel::class);
        $httpKernel->prependMiddleware(AttachCartCookie::class);
    }
}
