<?php

namespace App\models;

use App\DB_Connection;
use InvalidArgumentException;
use Exception;

/**
 * The PizzaCustom class extends the Pizza class and represents a specific custom pizza.
 * It manages the creation of custom pizzas either by loading from the database
 * or by manual initialization with provided values.
 * @package App\models
 * 
 * properties:
 * @property int $id
 * @property int $originalPizzaId
 * @property string $name
 * @property float $price
 * @property bool $spotlight
 * @property array $ingredients
 * 
 * methods:
 * @method static PizzaCustom getById(int $id)
 * @method static PizzaCustom[] getAllPizzas()
 * @method static string generateSpecificSection()
 * @method string __toString()
 * @method void __set(string $property, mixed $value)
 * @method mixed __get(string $property)
 * @method void __construct(?int $id = null, ?int $originalPizzaId = null, ?string $name = null, ?float $price = null, ?bool $spotlight = null, ?array $ingredients = null)
 */
class PizzaCustom extends Pizza
{
    /**
     * @var int Static counter to automatically generate unique identifiers.
     */
    private static int $autoIncrementId = 1;

    /**
     * @var int ID of the original pizza.
     */
    protected int $originalPizzaId;

    /**
     * Constructor for the PizzaCustom class.
     * 
     * @param int|null $id The unique identifier of the custom pizza.
     * @param int|null $originalPizzaId The unique identifier of the original pizza.
     * @throws Exception Throws an exception if the custom pizza cannot be loaded from the database.
     */
    public function __construct(
        ?int $id = null,
        ?int $originalPizzaId = null,
        ?string $name = null,
        ?float $price = null,
        ?bool $spotlight = null,
        ?array $ingredients = null
    ) {
        // If the ID is provided, load the pizza details from the database.
        if (!is_null($id)) {
            $this->loadFromDatabaseById($id);
        } else {
            // Otherwise, manually initialize if the information is provided.
            // The ID is automatically generated if null is passed.
            $this->id = self::$autoIncrementId++;
            parent::__construct($originalPizzaId, $name, $price, $spotlight, $ingredients);
        }
    }

    /**
     * Load the custom pizza details from the database.
     * 
     * @param int $id The unique identifier of the custom pizza.
     * @throws Exception Throws an exception if the custom pizza cannot be loaded from the database.
     */
    protected function loadFromDatabaseById(int $id): void
    {
        if (!is_numeric($id)) {
            throw new InvalidArgumentException("ID must be numeric");
        }

        $sql = "SELECT OriginalPizzaId FROM VIEW_CUSTOM_PIZZAS_WITH_INGREDIENTS WHERE CustomPizzaId = :id LIMIT 1";
        $res = DB_Connection::query($sql, ['id' => $id])[0]['OriginalPizzaId'];

        $this->originalPizzaId = $res;

        if (empty($this->originalPizzaId)) {
            throw new Exception("Custom Pizza not found with ID $id");
        }

        parent::__construct($this->originalPizzaId);
        $sql = "SELECT IngredientAddedId, IngredientRemovedId FROM VIEW_CUSTOM_PIZZAS_WITH_INGREDIENTS WHERE CustomPizzaId = :id";
        $customIngredients = DB_Connection::query($sql, ['id' => $id]);

        $additionalPrice = 0;
        foreach ($customIngredients as $ingredientDetails) {
            if ($ingredientDetails['IngredientAddedId']) {
                $additionalPrice += 1.50;
                array_push($this->ingredients, Ingredient::getById($ingredientDetails['IngredientAddedId']));
            }
            if ($ingredientDetails['IngredientRemovedId']) {
                $this->ingredients = array_filter($this->ingredients, function ($ingredient) use ($ingredientDetails) {
                    return $ingredient->id != $ingredientDetails['IngredientRemovedId'];
                });
            }
        }

        $this->price += $additionalPrice;
        $this->name .= ' Custom';
        $this->spotlight = false;
        $this->id = $id;
    }

    /**
     * Returns all custom pizzas from the database.
     * 
     * @return PizzaCustom[] An array of custom pizzas.
     */
    public function __get($property)
    {
        if ($property == 'originalPizzaId') {
            return $this->originalPizzaId;
        }
        return parent::__get($property);
    }

    /**
     * Magic setter to set protected properties of the class.
     * 
     * @param string $property The property name to set.
     * @param mixed $value The value to set the property to.
     * 
     * @throws InvalidArgumentException If the property does not exist or the value is of the incorrect type.
     * @return void
     */
    public function __set($property, $value)
    {
        if ($property == 'originalPizzaId') {
            if (!is_numeric($value)) {
                throw new InvalidArgumentException("ID must be numeric");
            }
            $this->originalPizzaId = $value;
        } else {
            parent::__set($property, $value);
        }
    }

    /**
     * Returns all custom pizzas from the database.
     * 
     * @return PizzaCustom[] An array of custom pizzas.
     */
    public static function getById(int $id): PizzaCustom
    {
        return new self($id);
    }
}
