<?php

namespace Freshbitsweb\CartManager\Middlewares;

use Illuminate\Support\Facades\Cookie;

class AttachCartCookie
{
    /**
     * Attach cookie to the response, if not attached already
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, \Closure $next)
    {
        // If this is an API request, we will not set cookie.
        if ($request->segment(1) == 'api') {
            // We will add random string to container for db entry
            app()->singleton('cart_manager_cookie', function () {
                return str_random(20);
            });
        } else if (! $request->hasCookie(config('cart_manager.cookie_name'))) {
            Cookie::queue(Cookie::make(config('cart_manager.cookie_name'), str_random(20), config('cart_manager.cookie_lifetime')));
        }

        return $next($request);
    }
}
