<?php

namespace App\models;

use App\DB_Connection;
use InvalidArgumentException;
use Exception;

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
        // D'abord, vérifier que l'ID est valide.
        if (!is_numeric($id)) {
            throw new InvalidArgumentException("ID must be numeric");
        }

        // Charger les détails de la pizza originale depuis la base de données.
        $sql = "SELECT OriginalPizzaId FROM VIEW_CUSTOM_PIZZAS_WITH_INGREDIENTS WHERE CustomPizzaId = :id LIMIT 1";
        $res = DB_Connection::query($sql, ['id' => $id])[0]['OriginalPizzaId'];

        $this->originalPizzaId = $res;

        if (empty($this->originalPizzaId)) {
            throw new Exception("Custom Pizza not found with ID $id");
        }

        parent::__construct($this->originalPizzaId);

        // Charger les ingrédients supplémentaires et les ingrédients supprimés
        $sql = "SELECT IngredientAddedId, IngredientRemovedId FROM VIEW_CUSTOM_PIZZAS_WITH_INGREDIENTS WHERE CustomPizzaId = :id";
        $customIngredients = DB_Connection::query($sql, ['id' => $id]);

        // Ajouter/supprimer les ingrédients et ajuster le prix
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

    public function __get($property)
    {
        if ($property == 'originalPizzaId') {
            return $this->originalPizzaId;
        }
        return parent::__get($property);
    }

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

    public static function getById(int $id): PizzaCustom
    {
        return new self($id);
    }
}
