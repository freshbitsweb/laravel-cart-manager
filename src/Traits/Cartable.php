<?php

namespace Freshbitsweb\CartManager\Traits;

use Freshbitsweb\CartManager\Core\Cart;

trait Cartable
{
    /**
     * Adds an item to the cart
     *
     * @param int Identifier
     * @return
     */
    public static function addToCart($id)
    {
        $class = static::class;

        return app(Cart::class)->add($class::findOrFail($id));
    }
}
