<?php

namespace Freshbitsweb\LaravelCartManager\Core;

use BadMethodCallException;
use Illuminate\Contracts\Support\Arrayable;
use Freshbitsweb\LaravelCartManager\Traits\CartTotals;
use Freshbitsweb\LaravelCartManager\Events\CartCleared;
use Freshbitsweb\LaravelCartManager\Events\CartCreated;
use Freshbitsweb\LaravelCartManager\Traits\Discountable;
use Freshbitsweb\LaravelCartManager\Contracts\CartDriver;
use Freshbitsweb\LaravelCartManager\Traits\CartItemsManager;

class Cart implements Arrayable
{
    use Discountable, CartTotals, CartItemsManager;

    protected $id = null;

    protected $cartDriver;

    protected $items = [];

    protected $subtotal = 0;

    protected $discount = 0;

    protected $discountPercentage = 0;

    protected $couponId = null;

    protected $shippingCharges = 0;

    protected $netTotal = 0;

    protected $tax = 0;

    protected $total = 0;

    protected $roundOff = 0;

    protected $payable = 0;

    /**
     * Sets object properties.
     *
     * @return void
     */
    public function __construct(CartDriver $cartDriver)
    {
        $this->cartDriver = $cartDriver;
        $this->items = collect($this->items);

        if ($cartData = $this->cartDriver->getCartData()) {
            $this->setItems($cartData['items']);
            unset($cartData['items']);

            $this->setProperties($cartData);
        }
    }

    /**
     * Sets the object properties from the provided data.
     *
     * @param array Cart attributes
     * @return void
     */
    protected function setProperties($attributes)
    {
        foreach ($attributes as $key => $value) {
            $this->{camel_case($key)} = $value;
        }
    }

    /**
     * Creates CartItem objects from the data.
     *
     * @param array Cart items data
     * @return void
     */
    protected function setItems($cartItems)
    {
        foreach($cartItems as $cartItem) {
            $this->items->push(CartItem::createFrom($cartItem));
        }
    }

    /**
     * Performs cart updates and returns the data.
     *
     * @param bool Weather its a new item or existing
     * @param bool Weather to keep the discount in the cart
     * @return array
     */
    protected function cartUpdates($isNewItem = false, $keepDiscount = false)
    {
        $this->updateTotals($keepDiscount);

        $this->storeCartData($isNewItem);

        return $this->toArray();
    }

    /**
     * Stores the cart data on the cart driver.
     *
     * @param bool Weather its a new item or an existing one
     * @return void
     */
    protected function storeCartData($isNewItem = false)
    {
        if ($this->id) {
            $this->cartDriver->updateCart($this->id, $this->data());

            if ($isNewItem) {
                $this->cartDriver->addCartItem($this->id, $this->items->last()->toArray());
            }

            return;
        }

        event(new CartCreated($this->toArray()));
        $this->cartDriver->storeNewCartData($this->toArray());
    }

    /**
     * Returns object properties as array.
     *
     * @param bool Weather items should also be covered
     * @return array
     */
    public function toArray($withItems = true)
    {
        $cartData = [
            'subtotal' => $this->subtotal,
            'discount' => $this->discount,
            'discountPercentage' => $this->discountPercentage,
            'couponId' => $this->couponId,
            'shippingCharges' => $this->shippingCharges,
            'netTotal' => $this->netTotal,
            'tax' => $this->tax,
            'total' => $this->total,
            'roundOff' => $this->roundOff,
            'payable' => $this->payable,
        ];

        if ($withItems) {
            $cartData['items'] = $this->items();
        }

        return $cartData;
    }

    /**
     * Returns the cart items.
     *
     * @return array
     */
    public function items($displayCurrency = false)
    {
        $items = $this->items->map->toArray();

        if (! $displayCurrency) {
            return $items->toArray();
        }

        setlocale(LC_MONETARY, config('cart_manager.LC_MONETARY'));

        return $items->map(function ($item) {
            $item['price'] = money_format('%n', $item['price']);

            return $item;
        })->toArray();
    }

    /**
     * Returns the cart data without items.
     *
     * @return array
     */
    public function data()
    {
        return $this->toArray($withItems = false);
    }

    /**
     * Returns the cart totals with currency.
     *
     * @return array
     */
    public function totals()
    {
        setlocale(LC_MONETARY, config('cart_manager.LC_MONETARY'));

        $totals = ['Subtotal' => money_format('%n', $this->subtotal)];

        if ($this->discount > 0) {
            $totals['Discount'] = money_format('%n', $this->discount);
        }

        if ($this->shippingCharges > 0) {
            $totals['Shipping charges'] = money_format('%n', $this->shippingCharges);
        }

        if ($this->subtotal != $this->netTotal) {
            $totals['Net total'] = money_format('%n', $this->netTotal);
        }

        $totals['Tax'] = money_format('%n', $this->tax);

        if ($this->roundOff != 0) {
            $totals['Total'] = money_format('%n', $this->total);
            $totals['Round off'] = money_format('%n', $this->roundOff);
        }

        $totals['Payable'] = money_format('%n', $this->payable);

        return $totals;
    }

    /**
     * Clears the cart details from the cart driver.
     *
     * @return void
     */
    public function clear()
    {
        $this->cartDriver->clearData();

        event(new CartCleared);
    }

    /**
     * Serves as a getter for cart properties.
     *
     * @param string Method name
     * @param array Arguments
     * @return mixed
     * @throws \BadMethodCallException
     */
    public function __call($method, $arguments)
    {
        $property = camel_case(str_replace_first('get', '', $method));

        if (property_exists($this, $property)) {
            return $this->$property;
        }

        throw new BadMethodCallException('Method [{$method}] does not exist. Check documentation please.');
    }

    /**
     * Manually set the user id of the customer.
     *
     * @param int User id
     * @return void
     */
    public function setUser($userId)
    {
        app()->singleton('cart_auth_user_id', function () use ($userId) {
            return $userId;
        });
    }

    /**
     * Returns whether cart is empty or not.
     *
     * @return bool
     */
    public function isEmpty()
    {
        return $this->items->isEmpty();
    }
}
