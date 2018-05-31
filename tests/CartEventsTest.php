<?php

namespace Freshbitsweb\LaravelCartManager\Test;

use Illuminate\Support\Facades\Event;
use Freshbitsweb\LaravelCartManager\Events\CartCreated;

class CartEventsTest extends TestCase
{
    public function setUp()
    {
        parent::setup();

        Event::fake();
    }


    /** @test */
    public function fire_cart_created_event()
    {
        $cart = $this->addACartItem();

        Event::assertDispatched(CartCreated::class, function ($e) use ($cart) {
            return $e->cartData['subtotal'] === $cart['subtotal'];
        });
    }
}