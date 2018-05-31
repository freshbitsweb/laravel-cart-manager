<?php

namespace Freshbitsweb\LaravelCartManager\Test;

use Freshbitsweb\LaravelCartManager\Test\Support\TestProduct;

class CartTest extends TestCase
{
    /** @test */
    public function clear_cart_removes_all_data()
    {
        $cart = $this->addACartItem($quantity = 5);

        cart()->clear();

        $this->assertTrue(cart()->isEmpty());
    }

    /** @test */
    public function totals_of_the_cart()
    {
        $this->addACartItem($quantity = 2, [
            'price' => 10,
        ]);

        cart()->applyDiscount(10);

        $this->assertSame(20, (int) cart()->getSubtotal());
        $this->assertSame(10, (int) cart()->getDiscountPercentage());
        $this->assertSame(2, (int) cart()->getDiscount());
        $this->assertSame(10, (int) cart()->getShippingCharges());
        $this->assertSame(28, (int) cart()->getNetTotal());
        $this->assertSame(1.68, (float) cart()->getTax());
        $this->assertSame(29.68, (float) cart()->getTotal());
        $this->assertSame(0.02, (float) cart()->getRoundOff());
        $this->assertSame(29.7, (float) cart()->getPayable());
    }

    /** @test */
    public function update_of_new_item_values()
    {
        $this->addACartItem($quantity = 1, [
            'price' => 10,
        ]);

        TestProduct::find(1)->update(['price' => 15]);

        $cart = cart()->refreshAllItemsData();

        $this->assertSame(15, (int) $cart['items'][0]['price']);
    }
}
