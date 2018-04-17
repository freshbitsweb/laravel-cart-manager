<?php

namespace Freshbitsweb\LaravelCartManager\Exceptions;

use InvalidArgumentException;

class ItemPriceMissing extends InvalidArgumentException
{
    public static function for($modelName)
    {
        return new static("The price for the item of the model '$modelName' could not be obtained. There should be price column or getPrice() method available on the model.");
    }
}
