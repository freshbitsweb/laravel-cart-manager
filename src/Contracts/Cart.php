<?php

namespace Freshbitsweb\LaravelCartManager\Contracts;

interface Cart
{
    /**
     * Get the items of the cart.
     */
    public function items();
}
