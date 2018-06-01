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

    // To set the currency symbol. We use php's native money_format() function
    // in combination with setlocale() to display currency with amounts
    'LC_MONETARY' => 'en_US.UTF-8',
];
