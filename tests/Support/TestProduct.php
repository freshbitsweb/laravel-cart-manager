<?php

namespace Freshbitsweb\LaravelCartManager\Test\Support;

use Illuminate\Database\Eloquent\Model;
use Freshbitsweb\LaravelCartManager\Traits\Cartable;

class TestProduct extends Model
{
    use Cartable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['price'];
}
