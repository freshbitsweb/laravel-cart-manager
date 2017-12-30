<?php

namespace Freshbitsweb\CartManager\Observers;

use Freshbitsweb\CartManager\Models\Cart;

class CartObserver
{
    /**
     * Listen to the Cart deleting event.
     *
     * @param \Freshbitsweb\CartManager\Models\Cart $cart
     * @return void
     */
    public function deleting(Cart $cart)
    {
        $cart->items()->delete();
    }
}
