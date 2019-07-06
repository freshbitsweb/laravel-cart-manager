<?php

return [
    // The driver that should be used to manage the cart (database/session/custom)
    'driver' => Freshbitsweb\LaravelCartManager\Drivers\DatabaseDriver::class,

    // The authentication guard that should be used to identify the logged in customer
    'auth_guard' => 'web',

    // Shipping charges are applied on order amount (subtotal - discount)
    'shipping_charges' => 10,

    // Specify the minimum order amount to avoid shipping charges
    'shipping_charges_threshold' => 100,

    // Tax amount is applied on net total (subtotal - discount + shipping charges)
    // subtotal, tax, net total and total are rounded to 2 decimals
    'tax_percentage' => 6,

    // Round off the total amount (net total + tax) to nearest (0 or 0.05 or 0.1 or 0.5 or 1)
    // Total amount is rounded off accordingly to come up the payable amount by the customer
    'round_off_to' => 0.05,

    // Name of the cookie that is used to identify a user session
    'cookie_name' => 'cart_identifier',

    // Number of minutes for which the cart cookie should be valid in user's browser
    'cookie_lifetime' => 10080, // one week

    // We use php's NumberFormatter class to display numbers as a currency value
    // Ref - https://www.php.net/manual/en/class.numberformatter.php
    // Locales list - https://stackoverflow.com/a/3191729/3113599
    'locale' => 'en_US',

    // Currency to display numbers with symbols - The 3-letter ISO 4217 currency code
    'currency' => 'USD',

    // For Database driver only: Number of hours for which the cart data is considered valid
    // You can run/schedule the lcm_cart:clear command to remove old/invalid data
    'cart_data_validity' => 24 * 7, // a week
];
