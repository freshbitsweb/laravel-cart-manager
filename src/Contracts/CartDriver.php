<?php

namespace Freshbitsweb\LaravelCartManager\Contracts;

interface CartDriver
{
    /**
     * Returns current cart data.
     *
     * @return array Cart data (including items)
     */
    public function getCartData();

    /**
     * Stores the cart and cart items data.
     *
     * @param array Cart data (including items)
     * @return void
     */
    public function storeNewCartData($cartData);

    /**
     * Updates the cart record with the new data.
     *
     * @param int Id of the cart
     * @param array Cart data (without items)
     * @return void
     */
    public function updateCart($cartId, $cartData);

    /**
     * Adds a new cart item to the cart.
     *
     * @param int Cart id
     * @param array Cart item data
     * @return void
     */
    public function addCartItem($cartId, $cartItem);

    /**
     * Removes a cart item from the cart.
     *
     * @param int Cart item id
     * @return void
     */
    public function removeCartItem($cartItemId);

    /**
     * Updates the quantity of the cart item.
     *
     * @param int Id of the cart item
     * @param int New quantity to be set
     * @return void
     */
    public function setCartItemQuantity($cartItemId, $newQuantity);

    /**
     * Updates the details of all the cart items.
     *
     * @param \Illuminate\Support\Collection Cart items
     * @return void
     */
    public function updateItemsData($items);

    /**
     * Clears all the cart details including cart items.
     *
     * @return void
     */
    public function clearData();
}
