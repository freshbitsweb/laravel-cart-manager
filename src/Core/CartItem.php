<?php

namespace Freshbitsweb\CartManager\Core;

use Freshbitsweb\CartManager\Exceptions\ItemNameMissing;
use Freshbitsweb\CartManager\Exceptions\ItemPriceMissing;
use Illuminate\Contracts\Support\Arrayable;

class CartItem implements Arrayable
{
    public $modelType;

    public $modelId;

    public $name;

    public $price;

    public $quantity = 1;

    /**
     * Creates a new cart item
     *
     * @param Illuminate\Database\Eloquent\Model
     * @return \Freshbitsweb\CartManager\Core\CartItem
     */
    public function __construct($entity)
    {
        $this->modelType = get_class($entity);
        $this->modelId = $entity->{$entity->getKeyName()};
        $this->setName($entity);
        $this->setPrice($entity);

        return $this;
    }

    /**
     * Creates a new cart item
     *
     * @param Illuminate\Database\Eloquent\Model
     * @return \Freshbitsweb\CartManager\Core\CartItem
     */
    public static function create($entity)
    {
        return new static($entity);
    }

    /**
     * Sets the name of the item
     *
     * @param Illuminate\Database\Eloquent\Model
     * @return void
     * @throws ItemNameMissing
     */
    protected function setName($entity)
    {
        if (method_exists($entity, 'getName')) {
            $this->name = $entity->getName();
            return;
        }

        if (property_exists($entity, 'name')) {
            $this->name = $entity->name;
            return;
        }

        throw ItemNameMissing::for($this->modelType);
    }

    /**
     * Sets the price of the item
     *
     * @param Illuminate\Database\Eloquent\Model
     * @return void
     * @throws ItemPriceMissing
     */
    protected function setPrice($entity)
    {
        if (method_exists($entity, 'getPrice')) {
            $this->price = $entity->getPrice();
            return;
        }

        if (isset($entity->price)) {
            $this->price = $entity->price;
            return;
        }

        throw ItemPriceMissing::for($this->modelType);
    }

    /**
     * Returns object properties as array
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'model_type' => $this->modelType,
            'model_id' => $this->modelId,
            'name' => $this->name,
            'price' => $this->price,
            'quantity' => $this->quantity,
        ];
    }
}
