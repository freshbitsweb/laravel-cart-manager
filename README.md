[![Latest Stable Version](https://poser.pugx.org/freshbitsweb/laravel-cart-manager/v/stable)](https://packagist.org/packages/freshbitsweb/laravel-cart-manager)
[![Total Downloads](https://poser.pugx.org/freshbitsweb/laravel-cart-manager/downloads)](https://packagist.org/packages/freshbitsweb/laravel-cart-manager)
[![License](https://poser.pugx.org/freshbitsweb/laravel-cart-manager/license)](https://packagist.org/packages/freshbitsweb/laravel-cart-manager)
[![StyleCI](https://styleci.io/repos/115199831/shield?branch=master)](https://styleci.io/repos/115199831)
[![Build Status](https://travis-ci.com/freshbitsweb/laravel-cart-manager.svg?branch=master)](https://travis-ci.com/freshbitsweb/laravel-cart-manager)

# Cart Manager for Laravel 5.5+
Let's make the cart management with Laravel a breeze.

## Just another shopping cart package?
There are a few well maintained shopping cart packages available but I wanted to have a solution which is more coupled with the database and provides additional functionality like **shipping charges**, **tax**, **total round off**, **user assignment**, etc. __out-of-box__ while staying a very easy to use package.


## Requirements

* PHP 7
* Laravel 5.5+

## Installation and setup

1) Install the package by running this command in your terminal/cmd:
```
composer require freshbitsweb/laravel-cart-manager
```

2) Import [config file](https://github.com/freshbitsweb/laravel-cart-manager/blob/master/config/cart_manager.php) by running this command in your terminal/cmd:
```
php artisan vendor:publish --tag=laravel-cart-manager-config
```
And set the options according to your requirements.

3) (For using [DatabaseDriver](https://github.com/freshbitsweb/laravel-cart-manager/blob/master/src/Drivers/DatabaseDriver.php) only) Import [migrations files](https://github.com/freshbitsweb/laravel-cart-manager/tree/master/database/migrations) by running this command in your terminal/cmd:
```
php artisan vendor:publish --tag=laravel-cart-manager-migrations
```
And [migrate](https://laravel.com/docs/master/migrations#running-migrations) your database to create necessary tables.

4) Add a trait to the model(s) of cart items:
```
...
use Freshbitsweb\LaravelCartManager\Traits\Cartable;
...
class Product extends Model
{
    use Cartable;
    ...
}
```

## Playing with cart
### Add to cart
```
Product::addToCart($productId);
```

### Remove from cart
```
cart()->removeAt($cartItemIndex);
```

### Increment/decrement quantity of a cart item
```
cart()->incrementQuantityAt($cartItemIndex);
cart()->decrementQuantityAt($cartItemIndex);
```

### Clear cart
```
cart()->clear();
```

## Applying discount
### Apply percentage discount
```
cart()->applyDiscount($percentage);
```

### Apply flat discount
```
cart()->applyFlatDiscount($discountAmount);
```

## Fetching cart data
### Get complete cart details
```
cart()->toArray();
```

### Get cart totals
```
cart()->data();
```

### Get cart items
```
cart()->items();
```


## Authors

* [**Gaurav Makhecha**](https://github.com/gauravmak) - *Initial work*

See also the list of [contributors](https://github.com/freshbitsweb/laravel-cart-manager/graphs/contributors) who participated in this project.

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details

## Special Thanks to

* [Laravel](https://laravel.com) Community
