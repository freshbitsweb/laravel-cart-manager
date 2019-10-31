<?php

namespace Freshbitsweb\LaravelCartManager\Models;

use Illuminate\Database\Eloquent\Model;
use Freshbitsweb\LaravelCartManager\Contracts\Cart as CartContract;

class Cart extends Model implements CartContract
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'cookie', 'auth_user', 'subtotal', 'discount', 'discount_percentage', 'coupon_id',
        'shipping_charges', 'net_total', 'tax', 'total', 'round_off', 'payable',
    ];

    /**
     * Get the items of the cart.
     */
    public function items()
    {
        return $this->hasMany(CartItem::class);
    }
}
