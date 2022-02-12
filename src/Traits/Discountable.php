<?php

namespace Freshbitsweb\LaravelCartManager\Traits;

use Freshbitsweb\LaravelCartManager\Events\DiscountApplied;
use Freshbitsweb\LaravelCartManager\Exceptions\IncorrectDiscount;

trait Discountable
{
    /**
     * Applies discount to the cart.
     *
     * @param float Discount Percentage
     * @param int Coupon id
     * @return array
     * @throws IncorrectDiscount
     */
    public function applyDiscount($percentage, $couponId = null)
    {
        if ($percentage > 100) {
            throw new IncorrectDiscount('Maximum percentage discount can be 100%.');
        }

        if ($this->subtotal == 0) {
            throw new IncorrectDiscount('Discount cannot be applied on an empty cart.');
        }

        $this->discountPercentage = $percentage;
        $this->couponId = $couponId;
        $this->discount = round(($this->subtotal * $percentage) / 100, 2);

        $cartData = $this->cartUpdates($isNewItem = false, $keepDiscount = true);

        event(new DiscountApplied($cartData));

        return $cartData;
    }

    /**
     * Applies flat discount to the cart.
     *
     * @param float Discount amount
     * @param int Coupon id
     * @return array
     * @throws IncorrectDiscount
     */
    public function applyFlatDiscount($amount, $couponId = null)
    {
        if ($amount > $this->subtotal) {
            throw new IncorrectDiscount('The discount amount cannot be more that subtotal of the cart');
        }

        $this->discount = round($amount, 2);
        $this->discountPercentage = 0;
        $this->couponId = $couponId;

        $cartData = $this->cartUpdates($isNewItem = false, $keepDiscount = true);

        event(new DiscountApplied($cartData));

        return $cartData;
    }
}
