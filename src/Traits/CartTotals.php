<?php

namespace Freshbitsweb\LaravelCartManager\Traits;

trait CartTotals
{
    /**
     * Sets the total variables of the object.
     *
     * @param bool Weather to keep the discount in the cart
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

        $this->netTotal = round($this->subtotal - $this->discount + $this->shippingCharges, 2);

        $this->tax = round(($this->netTotal * config('cart_manager.tax_percentage')) / 100, 2);

        $this->total = round($this->netTotal + $this->tax, 2);

        $this->setPayableAndRoundOff();
    }

    /**
     * Sets the subtotal of the cart.
     *
     * @return void
     */
    protected function setSubtotal()
    {
        $this->subtotal = round($this->items->sum(function ($cartItem) {
            return $cartItem->price * $cartItem->quantity;
        }), 2);
    }

    /**
     * Sets the shipping charges of the cart.
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
     * Sets the payable and round off amount of the cart.
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

        $this->roundOff = round($this->payable - $this->total, 2);
    }
}
