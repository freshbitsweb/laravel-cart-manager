<?php

namespace Freshbitsweb\LaravelCartManager\Test;

use Illuminate\Support\Facades\Event;
use Freshbitsweb\LaravelCartManager\Events\CartCreated;
use Freshbitsweb\LaravelCartManager\Test\Support\TestProduct;
use Freshbitsweb\LaravelCartManager\Events\CartItemAdded;

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

    /** @test */
    public function fire_cart_item_added_event()
    {
        $testProduct = factory(TestProduct::class)->create();

        TestProduct::addToCart($testProduct->id);

        Event::assertDispatched(CartItemAdded::class, function ($e) use ($testProduct) {
            return $e->entity->id === $testProduct->id;
        });
    }
}