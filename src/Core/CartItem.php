<?php

namespace Freshbitsweb\LaravelCartManager\Core;

use Freshbitsweb\LaravelCartManager\Exceptions\ItemNameMissing;
use Freshbitsweb\LaravelCartManager\Exceptions\ItemPriceMissing;
use Illuminate\Contracts\Support\Arrayable;

class CartItem implements Arrayable
{
    public $id = null;

    public $modelType;

    public $modelId;

    public $name;

    public $price;

    public $image = null;

    public $quantity = 1;

    /**
     * Creates a new cart item
     *
     * @param Illuminate\Database\Eloquent\Model|array
     * @param int Quantity of the item
     * @return \Freshbitsweb\LaravelCartManager\Core\CartItem
     */
    public function __construct($data, $quantity)
    {
        if (is_array($data)) {
            return $this->createFromArray($data);
        }

        return $this->createFromModel($data, $quantity);
    }

    /**
     * Creates a new cart item from a model instance
     *
     * @param Illuminate\Database\Eloquent\Model
     * @param int Quantity of the item
     * @return \Freshbitsweb\LaravelCartManager\Core\CartItem
     */
    protected function createFromModel($entity, $quantity)
    {
        $this->modelType = get_class($entity);
        $this->modelId = $entity->{$entity->getKeyName()};
        $this->setName($entity);
        $this->setPrice($entity);
        $this->setImage($entity);
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * Creates a new cart item from an array
     *
     * @param array
     * @return \Freshbitsweb\LaravelCartManager\Core\CartItem
     */
    protected function createFromArray($array)
    {
        $this->id = $array['id'];
        $this->modelType = $array['modelType'];
        $this->modelId = $array['modelId'];
        $this->name = $array['name'];
        $this->price = $array['price'];
        $this->image = $array['image'];
        $this->quantity = $array['quantity'];

        return $this;
    }

    /**
     * Creates a new cart item from an array or entity
     *
     * @param Illuminate\Database\Eloquent\Model|array
     * @param int Quantity of the item
     * @return \Freshbitsweb\LaravelCartManager\Core\CartItem
     */
    public static function createFrom($data, $quantity = 1)
    {
        return new static($data, $quantity);
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

        if ($entity->offsetExists('name')) {
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

        if ($entity->offsetExists('price')) {
            $this->price = $entity->price;
            return;
        }

        throw ItemPriceMissing::for($this->modelType);
    }

    /**
     * Sets the image of the item
     *
     * @param Illuminate\Database\Eloquent\Model
     * @return void
     */
    protected function setImage($entity)
    {
        if (method_exists($entity, 'getImage')) {
            $this->image = $entity->getImage();
            return;
        }

        if (isset($entity->image)) {
            $this->image = $entity->image;
        }
    }

    /**
     * Returns object properties as array
     *
     * @return array
     */
    public function toArray()
    {
        $cartItemData = [
            'modelType' => $this->modelType,
            'modelId' => $this->modelId,
            'name' => $this->name,
            'price' => $this->price,
            'image' => $this->image,
            'quantity' => $this->quantity,
        ];

        if ($this->id) {
            $cartItemData['id'] = $this->id;
        }

        return $cartItemData;
    }
}
