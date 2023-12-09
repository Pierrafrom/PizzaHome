<?php

namespace App\models;

use InvalidArgumentException;

/**
 * The Food class is an abstract class that defines the basic properties and methods
 * common to food items such as pizzas or desserts. It serves as a base class from which specific
 * food item classes can be derived.
 */
abstract class Food
{
    /**
     * @var int The unique identifier for the food item.
     */
    protected int $id;

    /**
     * @var string The name of the food item.
     */
    protected string $name;

    /**
     * @var float The price of the food item.
     */
    protected float $price;

    /**
     * @var bool Flag indicating whether the food item is featured or not.
     */
    protected bool $spotlight;

    /**
     * Magic getter to access protected properties of the class.
     *
     * @param string $property The property name to access.
     *
     * @return mixed The value of the property.
     * @throws InvalidArgumentException If the property does not exist.
     */
    public function __get(string $property)
    {
        if (property_exists($this, $property)) {
            return $this->$property;
        }
        throw new InvalidArgumentException("Invalid property: $property");
    }

    /**
     * Magic setter to set protected properties of the class.
     *
     * @param string $property The property name to set.
     * @param mixed  $value The value to set the property to.
     *
     * @throws InvalidArgumentException If the property does not exist or the value is of the incorrect type.
     */
    public function __set(string $property, mixed $value)
    {
        switch ($property) {
            case 'id':
                if (!is_int($value)) {
                    throw new InvalidArgumentException("id must be an integer.");
                }
                $this->id = $value;
                break;
            case 'name':
                if (!is_string($value)) {
                    throw new InvalidArgumentException("name must be a string.");
                }
                $this->$property = $value;
                break;
            case 'price':
                if (!is_float($value)) {
                    throw new InvalidArgumentException("price must be a float.");
                }
                $this->price = $value;
                break;
            case 'spotlight':
                if (!is_bool($value)) {
                    throw new InvalidArgumentException("spotlight must be a boolean.");
                }
                $this->spotlight = $value;
                break;
            default:
                throw new InvalidArgumentException("Invalid property: $property");
        }
    }
}
