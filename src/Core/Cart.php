<?php

namespace Freshbitsweb\CartManager\Core;

use Freshbitsweb\CartManager\Contracts\CartDriver;
use Illuminate\Contracts\Support\Arrayable;

class Cart implements Arrayable
{
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
     * Sets object properties
     *
     * @return void
     */
    public function __construct(CartDriver $cartDriver)
    {
        $this->cartDriver = $cartDriver;
        $this->items = collect($this->items);

        if ($cartData = $this->cartDriver->getCartData()) {
            $this->setItems($cartData->items);

            $this->setProperties($cartData->getAttributes());
        }
    }

    /**
     * Sets the object properties from the provided data
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
     * Creates CartItem objects from the data
     *
     * @param array Cart items data
     * @return void
     */
    protected function setItems($cartItems)
    {
        $cartItems->each(function ($cartItem) {
            $this->items->push(CartItem::createFrom($cartItem->toArray()));
        });
    }

    /**
     * Adds an item to the cart
     *
     * @param Illuminate\Database\Eloquent\Model
     * @return void
     */
    public function add($entity)
    {
        $this->items->push(CartItem::createFrom($entity));

        $this->updateTotals();

        $this->storeCartData();

        return $this->toArray();
    }

    /**
     * Sets the total variables of the object
     *
     * @param boolean Weather to keep the discount in the cart
     * @return void
     */
    protected function updateTotals($keepDiscount = false)
    {
        $this->setSubtotal();

        if (! $keepDiscount) {
            $this->discount = $this->discountPercentage = 0;
            $this->couponId = null;
        }

        $this->setShippingCharges();

        $this->netTotal = $this->subtotal - $this->discount + $this->shippingCharges;

        $this->tax = round(($this->netTotal * config('cart_manager.tax_percentage')) / 100, 2);

        $this->total = round($this->netTotal + $this->tax, 2);

        $this->setPayableAndRoundOff();
    }

    /**
     * Sets the subtotal of the cart
     *
     * @return void
     */
    protected function setSubtotal()
    {
        $this->subtotal = $this->items->sum(function ($cartItem) {
            return $cartItem->price * $cartItem->quantity;
        });
    }

    /**
     * Sets the shipping charges of the cart
     *
     * @return void
     */
    protected function setShippingCharges()
    {
        $this->shippingCharges = 0;
        $orderAmount = $this->subtotal - $this->discount;

        if ($orderAmount > 0 && $orderAmount < config('cart_manager.shipping_charges_threshold')) {
            $shippingCharges = config('cart_manager.shipping_charges');

            if ($shippingCharges > 0) {
                $this->shippingCharges = $shippingCharges;
            }
        }
    }

    /**
     * Sets the payable and round off amount of the cart
     *
     * @return void
     */
    protected function setPayableAndRoundOff()
    {
        switch (config('cart_manager.round_off_to')) {
            case 0.05:
                // https://stackoverflow.com/a/1592379/3113599
                $this->payable = round($this->total * 2, 1) / 2;
                break;

            case 0.1:
                $this->payable = round($this->total, 1);
                break;

            case 0.5:
                // http://www.kavoir.com/2012/10/php-round-to-the-nearest-0-5-1-0-1-5-2-0-2-5-etc.html
                $this->payable = round($this->total * 2) / 2;
                break;

            case 1:
                $this->payable = round($this->total);
                break;

            default:
                $this->payable = $this->total;
        }

        $this->roundOff = round($this->total - $this->payable, 2);
    }

    /**
     * Stores the cart data on the cart driver
     *
     * @return void
     */
    protected function storeCartData()
    {
        $this->cartDriver->storeCartData($this->toArray());
    }

    /**
     * Returns object properties as array
     *
     * @return array
     */
    public function toArray()
    {
        $cartData = [
            // First toArray() for CartItem object and second one for the Illuminate Collection
            'items' => $this->items->map->toArray()->toArray(),
            'subtotal' => $this->subtotal,
            'discount' => $this->discount,
            'discount_percentage' => $this->discountPercentage,
            'coupon_id' => $this->couponId,
            'shipping_charges' => $this->shippingCharges,
            'net_total' => $this->netTotal,
            'tax' => $this->tax,
            'total' => $this->total,
            'round_off' => $this->roundOff,
            'payable' => $this->payable,
        ];

        if ($this->id) {
            $cartData['id'] = $this->id;
        }

        return $cartData;
    }
}
