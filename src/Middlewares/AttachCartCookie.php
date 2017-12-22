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
        if (! $request->hasCookie(config('cart_manager.cookie_name'))) {
            Cookie::queue(Cookie::make(config('cart_manager.cookie_name'), str_random(20), config('cart_manager.cookie_lifetime')));
        }

        return $next($request);
    }
}
