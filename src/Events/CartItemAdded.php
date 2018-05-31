<?php

namespace Freshbitsweb\LaravelCartManager\Events;

class CartItemAdded
{
    /** @var Illuminate\Database\Eloquent\Model */
    public $entity;

    public function __construct($entity)
    {
        $this->entity = $entity;
    }
}
