<?php

namespace Freshbitsweb\CartManager\Exceptions;

use InvalidArgumentException;

class ItemNameMissing extends InvalidArgumentException
{
    public static function for($modelName)
    {
        return new static("The name for the item of the model '$modelName' could not be obtained. There should be name column or getName() method available on the model.");
    }
}
