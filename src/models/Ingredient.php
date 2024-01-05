<?php

namespace App\models;

use App\DB_Connection;
use Exception;

/**
 * The Ingredient class is an abstract class that defines the basic properties and methods
 * common to food items such as pizzas or desserts. It serves as a base class from which specific
 * food item classes can be derived.
 * @package App\models
 * 
 * properties:
 * @property int $id
 * @property string $name
 * @property float $stock
 * @property string $unit
 * @property bool $isAllergen
 * @property array $formFields
 * 
 * methods:
 * @method static Ingredient getById(int $id)
 * @method static Ingredient[] getAllIngredients()
 * @method static string generateSpecificSection()
 * @method string __toString()
 * @method void __set(string $property, mixed $value)
 * @method mixed __get(string $property)
 * @method void __construct(?int $id = null, ?string $name = null, ?int $stock = null, ?string $unit = null, ?bool $isAllergen = null)
 */
class Ingredient
{
    /**
     * @var int The unique identifier for the food item.
     */
    private static int $autoIncrementId = 1;

    /** 
     * @var int The unique identifier for the food item.
     */
    private int $id;

    /**
     * @var string The name of the food item.
     */
    private string $name;

    /**
     * @var float The price of the food item.
     */
    private float $stock;

    /**
     * @var bool Flag indicating whether the food item is featured or not.
     */
    private string $unit;

    /**
     * @var bool Flag indicating whether the food item is featured or not.
     */
    private bool $isAllergen;

    /**
     * @var array
     */
    public static $formFields = [
        'name' => ['type' => 'text', 'placeholder' => 'Ingredient Name', 'required' => true],
        'description' => ['type' => 'textarea', 'placeholder' => 'Ingredient Description', 'required' => false],
        'stock' => ['type' => 'number', 'placeholder' => 'Quantity in Stock', 'required' => true],
        'isAllergen' => ['type' => 'checkbox', 'required' => true],
    ];

    /**
     * Ingredient constructor.
     * 
     * @param int|null $id
     * @param string|null $name
     * @param float|null $stock
     * @param string|null $unit
     * @param bool|null $isAllergen
     */
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
     * Magic getter to access protected properties of the class.
     * 
     * @param string $property The property name to access.
     * 
     * @return mixed The value of the property.
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
     * Magic setter to set protected properties of the class.
     * 
     * @param string $property The property name to set.
     * @param mixed $value The value to set the property to.
     * 
     * @throws Exception
     * @return void
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

    /**
     * Returns all ingredients from the database.
     * 
     * @return array|object
     * @throws Exception 
     */
    public static function getAllIngredients(): object|array
    {
        $sql = "SELECT * FROM VIEW_INGREDIENT WHERE id <> 40";
        $ingredients = DB_Connection::query($sql, [], self::class);

        if (is_object($ingredients)) {
            return [$ingredients];
        }

        return $ingredients;
    }

    /**
     * Returns a string representation of the food item.
     * 
     * @return string
     */
    public function __toString()
    {
        if ($this->isAllergen) {
            return '<span style="color: var(--secondary);">' . $this->name . '*</span>';
        } else {
            return $this->name;
        }
    }

    /**
     * Returns the ingredient with the given id.
     * 
     * @param int $id The id of the ingredient to retrieve.
     * 
     * @return object
     * @throws Exception
     */
    public static function getById(int $id): object
    {
        $sql = "SELECT * FROM VIEW_INGREDIENT WHERE id = :id";
        $ingredient = DB_Connection::query($sql, ['id' => $id], self::class)[0];

        return $ingredient;
    }

    /**
     * Returns the HTML code for the specific section of the form.
     * 
     * @return string
     */
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
