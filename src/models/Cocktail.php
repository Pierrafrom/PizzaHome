<?php

namespace App\models;

use App\DB_Connection;
use Exception;
use InvalidArgumentException;

/**
 * The Cocktail class represents a specific cocktail.
 * It manages the creation of cocktails either by loading from the database
 * or by manual initialization with provided values.
 */
class Cocktail extends Food
{
    /**
     * @var int Static counter to automatically generate unique identifiers.
     */
    private static int $autoIncrementId = 1;

    /**
     * @var array An array of ingredients included in the cocktail item.
     */
    protected array $ingredients = [];

    /**
     * @var float alcohol percentage of the cocktail.
     */
    private float $alcoholPercentage;

    public static $formFields = [
        'name' => ['type' => 'text', 'placeholder' => 'Cocktail Name', 'required' => true],
        'price' => ['type' => 'number', 'placeholder' => 'Price', 'required' => true],
        'spotlight' => ['type' => 'checkbox', 'required' => false],
        'alcoholPercentage' => ['type' => 'number', 'placeholder' => 'Alcohol Percentage', 'required' => false]
    ];

    /**
     * Constructor for the Cocktail class.
     *
     * @param int|null $id The unique identifier of the cocktail. If null, an ID will be automatically generated.
     * @param string|null $name The name of the cocktail. If null, a default value will be used.
     * @param float|null $price The price of the cocktail. If null, a default value will be used.
     * @param bool|null $spotlight Indicates if the cocktail is featured. If null, a default value will be used.
     * @param array|null $ingredients The ingredients of the cocktail. If null, an empty array will be used.
     * @param float|null $alcoholPercentage The alcohol percentage of the cocktail. If null, a default value will be used.
     *
     * @throws Exception Throws an exception if the cocktail cannot be loaded from the database.
     *
     * @example
     * // To load an existing cocktail from the database:
     * $existingCocktail = new Cocktail(1);
     *
     * // To manually create a new cocktail without loading from the database:
     * $newCocktail = new Cocktail(null, "Margarita", 7.99, true, [['name' => 'Tomato', 'isAllergen' => false]]);
     */
    public function __construct(
        ?int    $id = null,
        ?string $name = null,
        ?float  $price = null,
        ?bool   $spotlight = null,
        ?array  $ingredients = null,
        ?float  $alcoholPercentage = null
    ) {
        // If the ID is provided, load the cocktail details from the database.
        if (!is_null($id)) {
            $this->loadFromDatabaseById($id);
        } else {
            // Otherwise, manually initialize if the information is provided.
            // The ID is automatically generated if null is passed.
            $this->id = self::$autoIncrementId++;
            // Use the null coalescence operator to provide default values.
            $this->name = $name ?? 'My Cocktail';
            $this->price = $price ?? 10.0;
            $this->spotlight = $spotlight ?? false;
            // Initialize the ingredients with an empty array if null is passed.
            $this->ingredients = $ingredients ?? [];
            $this->alcoholPercentage = $alcoholPercentage ?? 0.0;
        }
    }

    /**
     * Loads the details of a cocktail from the database using its unique identifier.
     *
     * @param int $id The unique identifier of the cocktail.
     *
     * @throws InvalidArgumentException If the ID is not numeric.
     * @throws Exception If the cocktail is not found in the database.
     */
    private function loadFromDatabaseById(int $id): void
    {
        // First check that the ID is valid.
        if (!is_numeric($id)) {
            throw new InvalidArgumentException("ID must be numeric");
        }

        // Retrieve the cocktail details from the database.
        $sql = "SELECT id, name, price, spotlight, alcoholPercentage 
                    FROM VIEW_COCKTAIL_INGREDIENTS 
                    WHERE id = :id GROUP BY id";
        $cocktailDetails = DB_Connection::query($sql, ['id' => $id]);

        // If the cocktail is found, initialize the properties of the object.
        if (!empty($cocktailDetails)) {
            $this->id = (int)$cocktailDetails[0]['id'];
            $this->name = $cocktailDetails[0]['name'];
            $this->price = (float)$cocktailDetails[0]['price'];
            $this->spotlight = (bool)$cocktailDetails[0]['spotlight'];
            $this->alcoholPercentage = (float)$cocktailDetails[0]['alcoholPercentage'];

            // Also load the cocktail's ingredients.
            $this->loadIngredients();
        } else {
            // Throw an exception if no cocktail is found with this ID.
            throw new Exception("Cocktail not found with ID $id");
        }
    }

    /**
     * Loads the ingredients of a cocktail from the database.
     */
    private function loadIngredients(): void
    {
        // Execute a query to get the specific cocktail's ingredients.
        $sql = "SELECT *
                FROM VIEW_INGREDIENT vi
                WHERE vi.id IN (
                    SELECT vc.ingredientId
                    FROM VIEW_COCKTAIL_INGREDIENTS vc
                    WHERE vc.id = :id
                )";
        $this->ingredients = DB_Connection::query($sql, ['id' => $this->id], Ingredient::class);
    }

    /**
     * Retrieves all cocktails from the database and returns them as Cocktail objects.
     *
     * @return array An array containing instances of Cocktail.
     * @throws Exception Throws an exception if the query fails.
     */
    public static function getAllCocktails(): array
    {
        // Query to get all the distinct cocktail IDs.
        $sql = "SELECT DISTINCT id FROM VIEW_COCKTAIL_INGREDIENTS";
        $cocktailIds = DB_Connection::query($sql);

        $cocktails = [];
        // For each ID found, create a new Cocktail instance.
        foreach ($cocktailIds as $row) {
            // Create a new Cocktail instance and load it from the database.
            $cocktails[] = new self($row['id']);
        }

        return $cocktails;
    }

    /**
     * Retrieves all cocktails that are set as spotlight from the database.
     *
     * @return array An array of Cocktail objects that are in the spotlight.
     * @throws Exception Throws an exception if the database query fails.
     */
    public static function getSpotlightCocktails(): array
    {
        // Define the SQL query to select cocktails where the spotlight is true.
        $sql = "SELECT id FROM VIEW_COCKTAIL_INGREDIENTS WHERE spotlight = 1 GROUP BY id";

        // Execute the query and store the results.
        $results = DB_Connection::query($sql);

        // Initialize an array to hold the spotlight cocktails.
        $spotlightCocktails = [];

        // Loop through the results and create a new Cocktail object for each one.
        foreach ($results as $row) {
            // Each cocktail is loaded from the database by its ID.
            $spotlightCocktails[] = new self((int)$row['id']);
        }

        // Return the array of spotlight Cocktail objects.
        return $spotlightCocktails;
    }

    /**
     * Displays cocktail information formatted for a menu.
     *
     * This function generates a string that includes the cocktail's name,
     * its ingredients (with allergens highlighted), and its price, all
     * formatted for display in a menu context.
     *
     * @return string The formatted menu item display string.
     *
     * @example Output Example:
     *          <h3>Cocktail Name</h3>
     *          <p><i>Ingredient1, <span style="color: var(--secondary);">Allergen *</span>, Ingredient2</i></p>
     *          <p><strong>€12.99</strong></p>
     *
     * The ingredients list is sanitized to prevent XSS attacks. Allergens
     * are specially marked with a red asterisk and styled in red. The cocktail
     * name and price are also sanitized and formatted.
     */
    public function displayInMenu(): string
    {
        // Initialize the output with the cocktail name wrapped in <h3> tags.
        $output = '<h4>' . htmlspecialchars($this->name) . '</h4>';

        // Join all the ingredients with a comma and wrap them in <i> tags for styling.
        $output .= '<p><i>' . implode(', ', $this->ingredients) . '</i></p>';

        // Append the price information, formatted with the euro sign and strong tags for emphasis.
        $output .= '<p><strong>€' . htmlspecialchars($this->price) . '</strong></p>';

        // Return the formatted string.
        return $output;
    }

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
        return parent::__get($property);
    }

    /**
     * Magic setter to set protected properties of the class.
     *
     * @param string $property The property name to set.
     * @param mixed $value The value to set the property to.
     *
     * @throws InvalidArgumentException If the property does not exist or the value is of the incorrect type.
     */
    public function __set(string $property, mixed $value)
    {
        if ($property === 'ingredients') {
            if (!is_array($value)) {
                throw new InvalidArgumentException("ingredients must be an array.");
            }
            $this->ingredients = $value;
        } elseif ($property === 'alcoholPercentage') {
            if (!is_numeric($value)) {
                throw new InvalidArgumentException("alcoholPercentage must be numeric.");
            }
            $this->alcoholPercentage = $value;
        } else {
            parent::__set($property, $value);
        }
    }

    /**
     * Retrieves a cocktail from the database using its unique identifier.
     *
     * @param int $id The unique identifier of the cocktail.
     *
     * @return Cocktail The cocktail object.
     * @throws Exception If the cocktail is not found in the database.
     */
    public static function getById(int $id): Cocktail
    {
        return new self($id);
    }
}
