[![Latest Stable Version](https://poser.pugx.org/freshbitsweb/laravel-cart-manager/v/stable)](https://packagist.org/packages/freshbitsweb/laravel-cart-manager)
[![Total Downloads](https://poser.pugx.org/freshbitsweb/laravel-cart-manager/downloads)](https://packagist.org/packages/freshbitsweb/laravel-cart-manager)
[![License](https://poser.pugx.org/freshbitsweb/laravel-cart-manager/license)](https://packagist.org/packages/freshbitsweb/laravel-cart-manager)
[![StyleCI](https://styleci.io/repos/115199831/shield?branch=master)](https://styleci.io/repos/115199831)
[![Build Status](https://travis-ci.com/freshbitsweb/laravel-cart-manager.svg?branch=master)](https://travis-ci.com/freshbitsweb/laravel-cart-manager)

# Cart Manager for Laravel 5.5+
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

## Detailed Documentation
Checkout the [full documentation](https://docs.freshbits.in/laravel-cart-manager).

## Tests
Run this command to run the tests of the package:
```
composer test
```

## Authors

* [**Gaurav Makhecha**](https://github.com/gauravmak) - *Initial work*

See also the list of [contributors](https://github.com/freshbitsweb/laravel-cart-manager/graphs/contributors) who participated in this project.

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details

## Special Thanks to

* [Laravel](https://laravel.com) Community
