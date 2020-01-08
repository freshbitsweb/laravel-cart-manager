<?php

namespace Freshbitsweb\LaravelCartManager\Test\Support;

use Freshbitsweb\LaravelCartManager\Traits\Cartable;
use Illuminate\Database\Eloquent\Model;

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
