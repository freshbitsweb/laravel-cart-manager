<?php

namespace Freshbitsweb\LaravelCartManager\Observers;

use Freshbitsweb\LaravelCartManager\Contracts\Cart as CartContract;

class CartObserver
{
    /**
     * Listen to the Cart deleting event.
     *
     * @param \Freshbitsweb\LaravelCartManager\Contracts\Cart $cart
     * @return void
     */
    public function deleting(CartContract $cart)
    {
        $cart->items()->delete();
    }
}
