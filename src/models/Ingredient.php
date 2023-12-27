<?php

namespace App\models;

use App\DB_Connection;
use Exception;

class Ingredient
{
    private static int $autoIncrementId = 1;
    private int $id;
    private string $name;
    private int $stock;
    private string $unit;
    private bool $isAllergen;

    public function __construct(
        ?int    $id = null,
        ?string $name = null,
        ?int    $stock = null,
        ?string $unit = null,
        ?bool   $isAllergen = null
    ) {
        if (!is_null($id)) {
            $this->id = self::$autoIncrementId++;
            $this->name = $name ?? 'My Ingredient';
            $this->stock = $stock ?? 0;
            $this->unit = $unit;
            $this->isAllergen = $isAllergen ?? false;
        }
    }

    /**
     * @throws Exception
     */
    public function __get($property)
    {
        if (property_exists($this, $property)) {
            return $this->$property;
        } else {
            throw new Exception("Property $property does not exist");
        }
    }

    /**
     * @throws Exception
     */
    public function __set($property, $value)
    {
        if (property_exists($this, $property)) {
            if ($property == 'id') {
                throw new Exception("Cannot set property $property");
            }
            $this->$property = $value;
        } else {
            throw new Exception("Property $property does not exist");
        }
    }

    public static function getAllIngredients(): object|array
    {
        $sql = "SELECT * FROM VIEW_INGREDIENT ";
        $ingredients = DB_Connection::query($sql, [], self::class);

        if (is_object($ingredients)) {
            return [$ingredients];
        }

        return $ingredients;
    }
}
