<?php

namespace Freshbitsweb\LaravelCartManager\Observers;

use Freshbitsweb\LaravelCartManager\Models\Cart;

class CartObserver
{
    /**
     * Listen to the Cart deleting event.
     *
     * @param  \Freshbitsweb\LaravelCartManager\Models\Cart  $cart
     * @return void
     */
    public function deleting(Cart $cart)
    {
        $cart->items()->delete();
    }
}
