<?php

namespace Freshbitsweb\LaravelCartManager\Events;

class CartCreated
{
    /** @var array */
    public $cartData;

    public function __construct($cartData)
    {
        $this->cartData = $cartData;
    }
}
