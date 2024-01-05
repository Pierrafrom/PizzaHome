<?php

namespace App\models;

use App\DB_Connection;
use Exception;

class Ingredient
{
    private static int $autoIncrementId = 1;
    private int $id;
    private string $name;
    private float $stock;
    private string $unit;
    private bool $isAllergen;

    public static $formFields = [
        'name' => ['type' => 'text', 'placeholder' => 'Ingredient Name', 'required' => true],
        'description' => ['type' => 'textarea', 'placeholder' => 'Ingredient Description', 'required' => false],
        'stock' => ['type' => 'number', 'placeholder' => 'Quantity in Stock', 'required' => true],
        'isAllergen' => ['type' => 'checkbox', 'required' => true],
    ];

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
            $this->stock = $stock ?? 0.00;
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
        $sql = "SELECT * FROM VIEW_INGREDIENT WHERE id <> 40";
        $ingredients = DB_Connection::query($sql, [], self::class);

        if (is_object($ingredients)) {
            return [$ingredients];
        }

        return $ingredients;
    }

    public function __toString()
    {
        if ($this->isAllergen) {
            return '<span style="color: var(--secondary);">' . $this->name . '*</span>';
        } else {
            return $this->name;
        }
    }

    public static function getById(int $id): object
    {
        $sql = "SELECT * FROM VIEW_INGREDIENT WHERE id = :id";
        $ingredient = DB_Connection::query($sql, ['id' => $id], self::class)[0];

        return $ingredient;
    }

    public static function generateSpecificSection()
    {
        $units = ['G', 'KG', 'L', 'CL', 'ML'];

        $html = '<div class="form-group">';
        $html .= '<label for="unit">Unité</label>';
        $html .= '<select id="unit" name="unit">';

        foreach ($units as $unit) {
            $html .= '<option value="' . htmlspecialchars($unit) . '">' . htmlspecialchars(ucfirst($unit)) . '</option>';
        }

        $html .= '</select>';
        $html .= '</div>';

        return $html;
    }
}
