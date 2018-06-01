<?php

namespace Freshbitsweb\LaravelCartManager\Drivers;

use Freshbitsweb\LaravelCartManager\Contracts\CartDriver;

class SessionDriver implements CartDriver
{
    public $sessionKey = 'lcm_cart';

    public $itemsKey = 'lcm_cart.items';

    /**
     * Returns current cart data.
     *
     * @return array Cart data (including items)
     */
    public function getCartData()
    {
        return session($this->sessionKey, ['items' => []]);
    }

    /**
     * Stores the cart and cart items data.
     *
     * @param array Cart data (including items)
     * @return void
     */
    public function storeNewCartData($cartData)
    {
        // Set id of the item to be used for updates later
        foreach ($cartData['items'] as $key => $item) {
            $cartData['items'][$key]['id'] = $key + 1;
        }

        // We use static number 1 as the cart id
        session([
            $this->sessionKey => array_merge($cartData, ['id' => 1]),
        ]);
    }

    /**
     * Updates the cart record with the new data.
     *
     * @param int Id of the cart
     * @param array Cart data (without items)
     * @return void
     */
    public function updateCart($cartId, $cartData)
    {
        $items = session($this->itemsKey);

        session([
            $this->sessionKey => array_merge($cartData, ['items' => $items]),
        ]);
    }

    /**
     * Adds a new cart item to the cart.
     *
     * @param int Cart id
     * @param array Cart item data
     * @return void
     */
    public function addCartItem($cartId, $cartItem)
    {
        $cartItem['id'] = count(session($this->itemsKey)) + 1;

        session()->push($this->itemsKey, $cartItem);
    }

    /**
     * Removes a cart item from the cart.
     *
     * @param int Cart item id
     * @return void
     */
    public function removeCartItem($cartItemId)
    {
        $items = collect(session($this->itemsKey));

        $items = $items->reject(function ($item) use ($cartItemId) {
            return $item['id'] == $cartItemId;
        });

        session([$this->itemsKey => $items]);
    }

    /**
     * Updates the quantity of the cart item.
     *
     * @param int Id of the cart item
     * @param int New quantity to be set
     * @return void
     */
    public function setCartItemQuantity($cartItemId, $newQuantity)
    {
        $items = collect(session($this->itemsKey));

        $items = $items->map(function ($item) use ($cartItemId, $newQuantity) {
            if ($item['id'] == $cartItemId) {
                $item['quantity'] = $newQuantity;
            }

            return $item;
        });

        session([$this->itemsKey => $items]);
    }

    /**
     * Updates the details of all the cart items.
     *
     * @param \Illuminate\Support\Collection Cart items
     * @return void
     */
    public function updateItemsData($items)
    {
        session([$this->itemsKey => $items->toArray()]);
    }

    /**
     * Clears all the cart details including cart items.
     *
     * @return void
     */
    public function clearData()
    {
        session()->forget($this->sessionKey);
    }
}
