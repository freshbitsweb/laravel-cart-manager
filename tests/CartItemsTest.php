<?php

namespace Freshbitsweb\LaravelCartManager\Test;

use Freshbitsweb\LaravelCartManager\Exceptions\ItemMissing;
use Freshbitsweb\LaravelCartManager\Test\Support\TestProduct;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CartItemsTest extends TestCase
{
    /** @test */
    public function do_not_allow_non_existing_cart_item()
    {
        $this->expectException(ModelNotFoundException::class);
        $cart = TestProduct::addToCart(1);
    }

    /** @test */
    public function add_new_cart_item()
    {
        $cart = $this->addACartItem();

        $this->assertCount(1, $cart['items']);
    }

    /** @test */
    public function add_a_new_cart_item_with_multiple_quantities()
    {
        $cart = $this->addACartItem($quantity = 3);

        $this->assertSame(3, $cart['items'][0]['quantity']);
    }

    /** @test */
    public function add_an_existing_cart_item()
    {
        $testProduct = factory(TestProduct::class)->create();

        TestProduct::addToCart($testProduct->id);

        $cart = TestProduct::addToCart($testProduct->id);

        $this->assertCount(1, $cart['items']);
        $this->assertSame(2, $cart['items'][0]['quantity']);
    }

    /** @test */
    public function error_on_non_existing_item_removal()
    {
        $this->expectException(ItemMissing::class);

        cart()->removeAt(0);
    }

    /** @test */
    public function remove_an_item_from_cart()
    {
        $this->addACartItem();

        $cart = cart()->removeAt(0);

        $this->assertTrue(cart()->isEmpty());
    }

    /** @test */
    public function increment_cart_item_quantity()
    {
        $this->addACartItem();

        $cart = cart()->incrementQuantityAt(0);

        $this->assertSame(2, $cart['items'][0]['quantity']);
    }

    /** @test */
    public function decrement_cart_item_quantity()
    {
        $this->addACartItem($quantity = 3);

        $cart = cart()->decrementQuantityAt(0);

        $this->assertSame(2, $cart['items'][0]['quantity']);
    }

    /** @test */
    public function remove_cart_item_on_decrement_last_quantity()
    {
        $this->addACartItem();

        $cart = cart()->decrementQuantityAt(0);

        $this->assertTrue(cart()->isEmpty());
    }
}
