<?php

namespace Freshbitsweb\CartManager\Core;

use BadMethodCallException;
use Freshbitsweb\CartManager\Contracts\CartDriver;
use Freshbitsweb\CartManager\Exceptions\IncorrectDiscount;
use Freshbitsweb\CartManager\Exceptions\ItemMissing;
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
     * @param int Quantity
     * @return array
     */
    public function add($entity, $quantity)
    {
        if ($this->itemExists($entity)) {
            $cartItemIndex = $this->items->search($this->cartItemsCheck($entity));

            return $this->incrementQuantityAt($cartItemIndex, $quantity);
        }

        $this->items->push(CartItem::createFrom($entity, $quantity));

        return $this->cartUpdates($isNewItem = true);
    }

    /**
     * Performs cart updates and returns the data
     *
     * @param boolean Weather its a new item or existing
     * @param boolean Weather to keep the discount in the cart
     * @return array
     */
    protected function cartUpdates($isNewItem = false, $keepDiscount = false)
    {
        $this->updateTotals($keepDiscount);

        $this->storeCartData($isNewItem);

        return $this->toArray();
    }

    /**
     * Checks if an item already exists in the cart
     *
     * @param Illuminate\Database\Eloquent\Model
     * @return boolean
     */
    protected function itemExists($entity)
    {
        return $this->items->contains($this->cartItemsCheck($entity));
    }

    /**
     * Checks if a cart item with the specified entity already exists
     *
     * @param Illuminate\Database\Eloquent\Model
     * @return \Closure
     */
    protected function cartItemsCheck($entity)
    {
        return function ($item) use ($entity) {
            return $item->modelType == get_class($entity) &&
                $item->modelId == $entity->{$entity->getKeyName()}
            ;
        };
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

        $this->netTotal = round($this->subtotal - $this->discount + $this->shippingCharges, 2);

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
        $this->subtotal = round($this->items->sum(function ($cartItem) {
            return $cartItem->price * $cartItem->quantity;
        }), 2);
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

        $this->roundOff = round($this->payable - $this->total, 2);
    }

    /**
     * Stores the cart data on the cart driver
     *
     * @param boolean Weather its a new item or existing
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

        $this->cartDriver->storeNewCartData($this->toArray());
    }

    /**
     * Returns object properties as array
     *
     * @param boolean Weather items should also be covered
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
     * Returns the cart items
     *
     * @return array
     */
    public function items()
    {
        // First toArray() for CartItem object and second one for the Illuminate Collection
        return $this->items->map->toArray()->toArray();
    }

    /**
     * Returns the cart data without items
     *
     * @return array
     */
    public function data()
    {
        return $this->toArray($withItems = false);
    }

    /**
     * Removes an item from the cart
     *
     * @param int index of the item
     * @return array
     */
    public function removeAt($cartItemIndex)
    {
        $this->existenceCheckFor($cartItemIndex);

        $this->cartDriver->removeCartItem($this->items[$cartItemIndex]->id);
        $this->items = $this->items->forget($cartItemIndex)->values(); // To reset the index

        return $this->cartUpdates();
    }

    /**
     * Throws an exception is the there is no item at the specified index
     *
     * @param int index of the item
     * @return void
     * @throws ItemMissing
     */
    protected function existenceCheckFor($cartItemIndex)
    {
        if (! $this->items->has($cartItemIndex)) {
            throw new ItemMissing("There is no item in the cart at the specified index.");
        }
    }

    /**
     * Increments the quantity of a cart item
     *
     * @param int Index of the cart item
     * @param int quantity to be increased
     * @return array
     */
    public function incrementQuantityAt($cartItemIndex, $quantity = 1)
    {
        $this->existenceCheckFor($cartItemIndex);

        $this->items[$cartItemIndex]->quantity += $quantity;

        $this->cartDriver->setCartItemQuantity(
            $this->items[$cartItemIndex]->id,
            $this->items[$cartItemIndex]->quantity
        );

        return $this->cartUpdates();
    }

    /**
     * Decrements the quantity of a cart item
     *
     * @param int Index of the cart item
     * @param int quantity to be decreased
     * @return array
     */
    public function decrementQuantityAt($cartItemIndex, $quantity = 1)
    {
        $this->existenceCheckFor($cartItemIndex);

        if ($this->items[$cartItemIndex]->quantity <= $quantity) {
            return $this->removeAt($cartItemIndex);
        }

        $this->items[$cartItemIndex]->quantity -= $quantity;

        $this->cartDriver->setCartItemQuantity(
            $this->items[$cartItemIndex]->id,
            $this->items[$cartItemIndex]->quantity
        );

        return $this->cartUpdates();
    }

    /**
     * Clears the cart details from the cart driver
     *
     * @return void
     */
    public function clear()
    {
        $this->cartDriver->clearData();
    }

    /**
     * Serves as a getter for cart properties
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
     * Applies disount to the cart
     *
     * @param double Discount Percentage
     * @param int Coupon id
     * @return array
     * @throws IncorrectDiscount
     */
    public function applyDiscount($percentage, $couponId = NULL)
    {
        if ($this->subtotal == 0) {
            throw new IncorrectDiscount("Discount cannot be applied on an empty cart");
        }

        $this->discountPercentage = $percentage;
        $this->couponId = $couponId;
        $this->discount = round(($this->subtotal * $percentage) / 100, 2);

        return $this->cartUpdates($isNewItem = false, $keepDiscount = true);
    }

    /**
     * Applies flat disount to the cart
     *
     * @param double Discount amount
     * @param int Coupon id
     * @return array
     * @throws IncorrectDiscount
     */
    public function applyFlatDiscount($amount, $couponId = null)
    {
        if ($amount > $this->subtotal) {
            throw new IncorrectDiscount("The discount amount cannot be more that subtotal of the cart");
        }

        $this->discount = round($amount, 2);
        $this->discountPercentage = 0;
        $this->couponId = $couponId;

        return $this->cartUpdates($isNewItem = false, $keepDiscount = true);
    }

    /**
     * Manually set the user id of the customer
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
}
