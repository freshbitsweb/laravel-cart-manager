<?php

namespace Freshbitsweb\CartManager\Models;

use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'cart_id', 'model_type', 'model_id', 'name', 'price', 'image', 'quantity'
    ];
}
