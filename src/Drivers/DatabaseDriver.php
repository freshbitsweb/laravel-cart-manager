<?php

namespace Freshbitsweb\CartManager\Drivers;

use Freshbitsweb\CartManager\Contracts\CartDriver;
use Freshbitsweb\CartManager\Models\Cart;
use Freshbitsweb\CartManager\Models\CartItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;

class DatabaseDriver implements CartDriver
{
    /**
     * Returns current cart data
     *
     * @return array
     */
    public function getCartData()
    {
        return [
            'items' => [],
            'subtotal' => 0,
            'discount' => 0,
            'discountPercentage' => 0,
            'couponId' => NULL,
            'shippingCharges' => 0,
            'netTotal' => 0,
            'tax' => 0,
            'total' => 0,
            'roundOff' => 0,
            'payable' => 0,
        ];
    }

    /**
     * Stores the cart and cart items data in the database tables
     *
     * @param array Cart data
     * @return void
     */
    public function storeCartData($cartData)
    {
        $cartItems = $cartData['items'];
        unset($cartData['items']);
        $cartId = $this->storeCartDetails($cartData);

        foreach($cartItems as $cartItem) {
            $cartItem['cart_id'] = $cartId;
            CartItem::create($cartItem);
        }
    }

    /**
     * Stores the cart data in the database table and returns the id of the record
     *
     * @param array Cart data
     * @return int
     */
    protected function storeCartDetails($cartData)
    {
        $cart = Cart::updateOrCreate(
            $this->cartIdentifier(),
            array_merge($cartData, $this->getCookieElement())
        );

        return $cart->id;
    }

    /**
     * Returns the cart identifier
     *
     * @return array
     */
    protected function cartIdentifier()
    {
        // If customer is logged in, use his identifier
        if (Auth::guard(config('cart_manager.auth_guard'))->check()) {
            return ['auth_user' => Auth::guard(config('cart_manager.auth_guard'))->id()];
        }

        return $this->getCookieElement();
    }

    /**
     * Returns the cookie for the cart identification
     *
     * @return array
     */
    protected function getCookieElement()
    {
        return ['cookie' => Cookie::get(config('cart_manager.cookie_name'))];
    }
}
