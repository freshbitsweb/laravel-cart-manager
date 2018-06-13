<?php

namespace Freshbitsweb\LaravelCartManager\Test;

use Freshbitsweb\LaravelCartManager\Exceptions\IncorrectDiscount;

class CartDiscountTest extends TestCase
{
    /** @test */
    public function do_not_allow_discount_on_an_empty_cart()
    {
        $this->expectException(IncorrectDiscount::class);

        cart()->applyDiscount(10);
    }

    /** @test */
    public function do_not_allow_discount_more_than_100_percent()
    {
        $this->addACartItem();

        $this->expectException(IncorrectDiscount::class);

        cart()->applyDiscount(101);
    }

    /** @test */
    public function apply_percentage_discount_on_the_cart()
    {
        $this->addACartItem($quantity = 1, [
            'price' => 20,
        ]);

        $cart = cart()->applyDiscount(10);

        $this->assertSame(10, $cart['discountPercentage']);
        $this->assertSame(2, (int) $cart['discount']);
    }

    /** @test */
    public function apply_flat_discount_on_the_cart()
    {
        $this->addACartItem($quantity = 1, [
            'price' => 20,
        ]);

        $cart = cart()->applyFlatDiscount(2);

        $this->assertSame(0, $cart['discountPercentage']);
        $this->assertSame(2, (int) $cart['discount']);
    }

    /** @test */
    public function discount_removal_after_cart_updates()
    {
        $this->addACartItem();

        cart()->applyDiscount(10);

        $cart = $this->addACartItem();

        $this->assertSame(0, $cart['discountPercentage']);
    }
}
