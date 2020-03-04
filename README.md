[![Latest Stable Version](https://poser.pugx.org/freshbitsweb/laravel-cart-manager/v/stable)](https://packagist.org/packages/freshbitsweb/laravel-cart-manager)
[![Total Downloads](https://poser.pugx.org/freshbitsweb/laravel-cart-manager/downloads)](https://packagist.org/packages/freshbitsweb/laravel-cart-manager)
[![License](https://poser.pugx.org/freshbitsweb/laravel-cart-manager/license)](https://packagist.org/packages/freshbitsweb/laravel-cart-manager)
[![StyleCI](https://styleci.io/repos/115199831/shield?branch=master)](https://styleci.io/repos/115199831)
[![Build Status](https://travis-ci.com/freshbitsweb/laravel-cart-manager.svg?branch=master)](https://travis-ci.com/freshbitsweb/laravel-cart-manager)
[![Buy us a tree](https://img.shields.io/badge/Buy%20me%20a%20tree-%F0%9F%8C%B3-lightgreen?style=flat-square)](https://offset.earth/treeware?gift-trees)

# Cart Manager (Laravel 5.5+)
Let's make the cart management with Laravel a breeze.

## Just another shopping cart package?
There are a few well maintained shopping cart packages available but I wanted to have a solution which feels like *the Laravel way* and is more coupled with the database and provides additional functionality like **shipping charges**, **discount**, **tax**, **total**,  **round off**, **guest carts**, etc. *out-of-box* while staying a very easy to use package.

## Why/when to use?
Let us decide when this package should be used:

1. You are looking for an easy to use solution which provides cart feature for users as well as guests.
2. You want to make sure that the carting can work via APIs as well to support mobile applications.
3. You want features like Shipping charges, tax, discount, round off, etc.
4. You want to store cart data in Database, session or at a custom place.
5. You like using the packages which are more like [the Laravel way](https://laravelshift.com/opinionated-laravel-way-shift)

## Requirements

* PHP 7.2.5+
* Laravel 5.5+

**Notes**:
- If you are still using PHP <7.2.5 with <Laravel 6.x, you may use v1.2.0 of this package.

## Installation

1) Install the package by running this command in your terminal/cmd:
```
composer require freshbitsweb/laravel-cart-manager
```

2) Import config file by running this command in your terminal/cmd:
```
php artisan vendor:publish --tag=laravel-cart-manager-config
```

3) Import migrations files by running these commands in your terminal/cmd:
```
php artisan vendor:publish --tag=laravel-cart-manager-migrations
php artisan migrate
```

4) Add a trait to the model(s) of cart items:

```php
<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Freshbitsweb\LaravelCartManager\Traits\Cartable;

class Product extends Model
{
    use Cartable;
    // ...
}
```


## Usage - As Easy as 1 2 3
```php
// Add to cart
$cart = Product::addToCart($productId);

// Remove from cart
$cart = cart()->removeAt($cartItemIndex);

// Apply discount
$cart = cart()->applyDiscount($percentage);

// Fetch cart
$cart = cart()->toArray();
```

## Online Demo

The demo of the package can be found at - https://laravel-cart-manager.freshbits.in

## Requirements
PHP 7.0.0+
Laravel 5.5+

## Table of contents
* [Configuration Options](#configuration-options)
* [Drivers](#drivers)
* Using the Package
    * [Cart Management](#cart-management)
    * [Fetching Cart Data](#fetching-cart-data)
    * [Discounts](#discounts)
* Advanced
    * [Updates in Item Prices](#updates-in-item-prices)
    * [Using With API](#using-with-api)
    * [Events](#events)
    * [Commands](#commands)

## Configuration Options

`cart_manager.php` file contains the following config options for the package:

1. **driver :** *(default: DatabaseDriver)*
The [driver](#drivers) that should be used to store and retrieve cart details. You can use existing ones or create your own.

2. **auth_guard :** *(default: web)*
The authentication guard that should be used to identify the logged in customer. This package can store carts for guest users as well as logged in users.

3. **shipping_charges :** *(default: 10)*
The amount that should be applied as shipping of the order.

4. **shipping_charges_threshold :** *(default: 100)*
The minimum order amount to avoid the shipping charges. Take a note that order amount is calculated as subtotal of the cart items - discount amount.

5. **tax_percentage :** *(default: 6%)*
Tax is applied on subtotal of the cart items - discount amount + shipping charges and rounded to 2 decimals.

6. **round_off_to :** *(default: 0.05)*
You may wish to round of the order amount to the nearest decimal point. Options are (0 or 0.05 or 0.1 or 0.5 or 1)

7. **cookie_name :** *(default: cart_identifier)*
The name of the cookie that this package stores to identify the guests of the web app and store their cart data.

8. **cookie_lifetime :** *(default: 1 week)*
Number of minutes for which the cart cookie should be valid in customer's browser.

9. **LC_MONETARY :** *(default: en_US.UTF-8)*
This option is used to display the various totals of the cart with a currency symbol. We use php's native [money_format()](//php.net/manual/en/function.money-format.php) function to display currency with amount.

10. **cart_data_validity :** *(default: 1 week) (Database driver only)*
You may wish to remove old/invalid cart data from the database. You can specify the validity period and run/schedule the [ClearCartDataCommand](#commands) for the same.

**[⬆ back to top](#table-of-contents)**

## Drivers

You can  set the driver that should be used to store and retrieve cart details in the `cart_manager.php` config file. You can use existing ones or create your own driver.

### Database Driver

Database driver stores the cart data in 2 tables: `carts` and `cart_items`.  You can also remove stale data by running `ClearCartDataCommand`.

Using this driver allows you to store cart data on server and customer can be displayed the same cart across channels i.e. Mobile app, website, etc.

### Session Driver

This driver stores the cart data in the session according to the [session driver](https://laravel.com/docs/5.6/session#introduction). This driver does not support cart management for guests via API as we cannot have a uniform way to track the user.

**[⬆ back to top](#table-of-contents)**

## Cart Management

All of these operations return full cart data with items.

### Add to cart
```
/**
 * Add to cart
 *
 * @return json
 */
 public function addToCart()
{
    return Product::addToCart(request('productId'));
}
```

### Remove from cart
```
/**
 * Remove from cart
 *
 * @return json
 */
public function removeFromCart()
{
    return cart()->removeAt(request('cartItemIndex'));
}
```

### Increment/decrement quantity of a cart item
```
/**
 * Increment cart item quantity
 *
 * @return json
 */
public function incrementCartItem()
{
    return cart()->incrementQuantityAt(request('cartItemIndex'));
}

/**
 * Decrement cart item quantity
 *
 * @return json
 */
public function decrementCartItem()
{
    return cart()->decrementQuantityAt(request('cartItemIndex'));
}
```

### Clear cart
```
/**
 * Clear Cart
 *
 * @return json
 */
public function clearCart()
{
    return cart()->clear();
}
```

**[⬆ back to top](#table-of-contents)**

## Fetching Cart Data

### Get complete cart details
```
$cart = cart()->toArray();
```

### Get cart attributes
```
$cartAttributes = cart()->data();
```

### Get cart attributes with currency amount
```
$cartTotals = cart()->totals();
```

### Get cart individual attributes
Cart has following attributes: `subtotal`, `discount`, `discountPercentage`, `couponId`, `shippingCharges`, `netTotal`, `tax`, `total`, `roundOff`, `payable`.

You can access any of them using a getter method. For example,

```
$subtotal = cart()->getSubtotal();
```

### Get cart items
```
$cartItems = cart()->items();
```

### Get cart items with currency amount
```
$cartItems = cart()->items($displayCurrency = true);
```

![](/images/fetching-data.png)

**[⬆ back to top](#table-of-contents)**

## Discounts

### Apply percentage discount
```
$cart = cart()->applyDiscount($percentage);
```

### Apply flat discount
```
$cart = cart()->applyFlatDiscount($discountAmount);
```

![](/images/apply-discount.png)

**[⬆ back to top](#table-of-contents)**

## Updates in Item Prices

As this package stores the details of the cart items in a separate table or session, the cart data will not be updated if you update, price, name or image of the cart items.

If you update any of the item details regularly, we suggest you to run the following code before the final checkout to make sure that order totals are up-to-date as per the latest prices.

```
cart()->refreshAllItemsData();
```

This code will go through each cart item and update to fresh details.

**[⬆ back to top](#table-of-contents)**

## Using With API

**Note:** This feature is for Database driver only.

This package uses the cookies and sessions to maintain cart data during page reloads. As the APIs are stateless, we cannot use them to do the same.

To solve the issue, you can manually set the authenticated user id to maintain the cart data.

```
cart()->setUser($userId);
```

Running this code will tell the package to assign the cart data to the specified user.

**Note:** Guests cannot manage their carts via API as we cannot have a uniform way to track the user.

**[⬆ back to top](#table-of-contents)**

## Events

Working with Laravel, how can we forget events?

This package fires various cart related events which you can listen to for any application updates.

1. CartCreated
-> Fired when cart is created for the session for the first time and contains the full cart data in the variable `$cartData`.

2. CartItemAdded
-> Fired when an item is added to the cart and contains the new item Eloquent model object in the variable `$entity`.

3. CartItemRemoved
-> Fired when an item is removed from the cart and contains the new item Eloquent model object in the variable `$entity`.

4. DiscountApplied
-> Fired when discount if applied to the cart and contains the full cart data in the variable `$cartData`.

5. CartCleared
-> Fired when the cart is cleared.

### Sample Usage

Add the event and listener entry in the `EventServiceProvider` class
```
protected $listen = [
	'Freshbitsweb\LaravelCartManager\Events\CartCreated' => [
		'App\Listeners\LogCartCreated',
	],
];
```

Create respective listener:
```
<?php

namespace App\Listeners;

use Illuminate\Support\Facades\Log;
use Freshbitsweb\LaravelCartManager\Events\CartCreated;

class LogCartCreated
{
	/**
	 * Handle the event.
	 *
	 * @param CartCreated $event
	 * @return void
	 */
	public function handle(CartCreated  $event)
	{
		Log::info('cart', [$event->cartData]);
	}
}
```

**[⬆ back to top](#table-of-contents)**

## Commands

**Note:** This is required for Database driver only.

You may wish to remove old/invalid cart data from the database.

Schedule the ClearCartDataCommand for the same.

```
protected function schedule(Schedule $schedule)
{
    $schedule->command('lcm_carts:clear_old')->daily();
}
```

This will delete the old/invalid data which is considered based on the `cart_data_validity` config option.

**[⬆ back to top](#table-of-contents)**

## Tests
Run this command to run the tests of the package:
```
composer test
```

## Authors

* [**Gaurav Makhecha**](https://github.com/gauravmak) - *Initial work*

See also the list of [contributors](https://github.com/freshbitsweb/laravel-cart-manager/graphs/contributors) who participated in this project.

**[⬆ back to top](#table-of-contents)**

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details

## Treeware

You're free to use this package, but if it makes it to your production environment I would highly appreciate you buying the world a tree.

It’s now common knowledge that one of the best tools to tackle the climate crisis and keep our temperatures from rising above 1.5C is to <a href="https://www.bbc.co.uk/news/science-environment-48870920">plant trees</a>. If you contribute to our forest you’ll be creating employment for local families and restoring wildlife habitats.

You can buy trees at for our forest here [offset.earth/treeware](https://offset.earth/treeware?gift-trees)

Read more about Treeware at [treeware.earth](http://treeware.earth)

## Special Thanks to

* [Laravel](https://laravel.com) Community
