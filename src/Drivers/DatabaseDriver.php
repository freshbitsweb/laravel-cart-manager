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
        $selectColumns = ['id', 'subtotal', 'discount', 'discount_percentage', 'coupon_id', 'shipping_charges', 'net_total', 'tax', 'total', 'round_off', 'payable'];

        $cartItemColumns = 'items:id,cart_id,model_type,model_id,name,price,quantity';

        $cartData = Cart::with($cartItemColumns)->where($this->cartIdentifier())->first($selectColumns);

        // If there is no cart record for the logged in customer, try with cookie identifier
        if (! $cartData && Auth::guard(config('cart_manager.auth_guard'))->check()) {
            if ($cartData = Cart::with($cartItemColumns)->where($this->getCookieElement())->first($selectColumns)) {
                $this->assignCustomerToCartRecord();
            }
        }

        return $cartData;
    }

    /**
     * Assigns the customer to the cart record identified by cookie
     *
     * @return void
     */
    protected function assignCustomerToCartRecord()
    {
        // Assign the logged in customer to the cart record
        Cart::where($this->getCookieElement())->update([
            'auth_user' => Auth::guard(config('cart_manager.auth_guard'))->id()
        ]);
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
