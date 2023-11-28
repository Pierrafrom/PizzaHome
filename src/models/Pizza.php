<?php

namespace App\models;

use App\DB_Connection;
use Exception;
use InvalidArgumentException;

/**
 * The Pizza class extends the Food class and represents a specific pizza.
 * It manages the creation of pizzas either by loading from the database
 * or by manual initialization with provided values.
 */
class Pizza extends Food
{
    /**
     * @var int Static counter to automatically generate unique identifiers.
     */
    private static int $autoIncrementId = 1;

    /**
     * @var array An array of ingredients included in the pizza item.
     */
    private array $ingredients = [];

    /**
     * Constructor for the Pizza class.
     *
     * @param int|null $id The unique identifier of the pizza. If null, an ID will be automatically generated.
     * @param string|null $name The name of the pizza. If null, a default value will be used.
     * @param float|null $price The price of the pizza. If null, a default value will be used.
     * @param bool|null $spotlight Indicates if the pizza is featured. If null, a default value will be used.
     * @param array|null $ingredients The ingredients of the pizza. If null, an empty array will be used.
     *
     * @throws Exception Throws an exception if the pizza cannot be loaded from the database.
     *
     * @example
     * // To load an existing pizza from the database:
     * $existingPizza = new Pizza(1);
     *
     * // To manually create a new pizza without loading from the database:
     * $newPizza = new Pizza(null, "Margarita", 7.99, true, [['name' => 'Tomato', 'isAllergen' => false]]);
     */
    public function __construct(?int $id = null, ?string $name = null, ?float $price = null, ?bool $spotlight = null, ?array $ingredients = null)
    {
        // If the ID is provided, load the pizza details from the database.
        if (!is_null($id)) {
            $this->loadFromDatabaseById($id);
        } else {
            // Otherwise, manually initialize if the information is provided.
            // The ID is automatically generated if null is passed.
            $this->id = self::$autoIncrementId++;
            // Use the null coalescence operator to provide default values.
            $this->name = $name ?? 'My Pizza';
            $this->price = $price ?? 10.0;
            $this->spotlight = $spotlight ?? false;
            // Initialize the ingredients with an empty array if null is passed.
            $this->ingredients = $ingredients ?? [];
        }
    }

    /**
     * Loads the details of a pizza from the database using its unique identifier.
     *
     * @param int $id The unique identifier of the pizza.
     *
     * @throws InvalidArgumentException If the ID is not numeric.
     * @throws Exception If the pizza is not found in the database.
     */
    private function loadFromDatabaseById(int $id): void
    {
        // First check that the ID is valid.
        if (!is_numeric($id)) {
            throw new InvalidArgumentException("ID must be numeric");
        }

        // Retrieve the pizza details from the database.
        $sql = "SELECT id, name, price, spotlight FROM VIEW_PIZZA_INGREDIENTS WHERE id = :id GROUP BY id";
        $pizzaDetails = DB_Connection::query($sql, ['id' => $id]);

        // If the pizza is found, initialize the properties of the object.
        if (!empty($pizzaDetails)) {
            $this->id = (int)$pizzaDetails[0]['id'];
            $this->name = $pizzaDetails[0]['name'];
            $this->price = (float)$pizzaDetails[0]['price'];
            $this->spotlight = (bool)$pizzaDetails[0]['spotlight'];

            // Also load the pizza's ingredients.
            $this->loadIngredients();
        } else {
            // Throw an exception if no pizza is found with this ID.
            throw new Exception("Pizza not found with ID $id");
        }
    }

    /**
     * Loads the ingredients of a pizza from the database.
     */
    private function loadIngredients(): void
    {
        // Execute a query to get the specific pizza's ingredients.
        $sql = "SELECT ingredientName, isAllergen FROM VIEW_PIZZA_INGREDIENTS WHERE id = :id";
        $ingredients = DB_Connection::query($sql, ['id' => $this->id]);

        // Populate the Pizza object's ingredients array.
        foreach ($ingredients as $ingredient) {
            $this->ingredients[] = [
                'name' => $ingredient['ingredientName'],
                'isAllergen' => (bool)$ingredient['isAllergen'],
            ];
        }
    }

    /**
     * Retrieves all pizzas from the database and returns them as Pizza objects.
     *
     * @return array An array containing instances of Pizza.
     * @throws Exception Throws an exception if the query fails.
     */
    public static function getAllPizzas(): array
    {
        // Query to get all the distinct pizza IDs.
        $sql = "SELECT DISTINCT id FROM VIEW_PIZZA_INGREDIENTS";
        $pizzaIds = DB_Connection::query($sql);

        $pizzas = [];
        // For each ID found, create a new Pizza instance.
        foreach ($pizzaIds as $row) {
            // Create a new Pizza instance and load it from the database.
            $pizzas[] = new self($row['id']);
        }

        return $pizzas;
    }

    /**
     * Retrieves all pizzas that are set as spotlight from the database.
     *
     * @return array An array of Pizza objects that are in the spotlight.
     * @throws Exception Throws an exception if the database query fails.
     */
    public static function getSpotlightPizzas(): array
    {
        // Define the SQL query to select pizzas where the spotlight is true.
        $sql = "SELECT id FROM VIEW_PIZZA_INGREDIENTS WHERE spotlight = 1 GROUP BY id";

        // Execute the query and store the results.
        $results = DB_Connection::query($sql);

        // Initialize an array to hold the spotlight pizzas.
        $spotlightPizzas = [];

        // Loop through the results and create a new Pizza object for each one.
        foreach ($results as $row) {
            // Each pizza is loaded from the database by its ID.
            $spotlightPizzas[] = new self((int)$row['id']);
        }

        // Return the array of spotlight Pizza objects.
        return $spotlightPizzas;
    }

    /**
     * Generates a string containing a comma-separated list of ingredient names
     * for the pizza, enclosed within a paragraph HTML element.
     *
     * The method maps over the `$ingredients` array property, extracting the
     * 'name' of each ingredient. It then concatenates these names into a
     * single string separated by commas. The resultant string is sanitized
     * using `htmlspecialchars()` to prevent XSS attacks when displayed in HTML.
     *
     * @return string Returns a string of ingredient names wrapped in a paragraph tag.
     *                The special characters in the ingredient names are converted to HTML entities.
     */
    public function getDescription(): string
    {
        $ingredientNames = array_map(function ($ingredient) {
            return $ingredient['name'];
        }, $this->ingredients);

        return '<p>' . htmlspecialchars(implode(', ', $ingredientNames)) . '</p>';
    }

    /**
     * Constructs a lowercase, URL-friendly version of the pizza's name.
     *
     * This method takes the pizza's name, replaces all spaces with hyphens,
     * and converts the entire string to lowercase. This is useful for creating
     * clean, readable URLs or file names that require lowercase characters
     * and no spaces.
     *
     * @return string The pizza's name in lowercase with spaces replaced by hyphens.
     */
    public function getImageName(): string
    {
        // First, remove all content within parentheses, including the parentheses themselves
        $nameWithoutParentheses = preg_replace('/\s*\([^)]*\)/', '', $this->name);
        // Then replace spaces with hyphens and convert to lowercase
        return strtolower(str_replace(' ', '-', $nameWithoutParentheses));
    }

    /**
     * Displays pizza information formatted for a menu.
     *
     * This function generates a string that includes the pizza's name,
     * its ingredients (with allergens highlighted), and its price, all
     * formatted for display in a menu context.
     *
     * @return string The formatted menu item display string.
     *
     * @example Output Example:
     *          <h3>Pizza Name</h3>
     *          <p><i>Ingredient1, <span style="color: var(--secondary);">Allergen *</span>, Ingredient2</i></p>
     *          <p><strong>€12.99</strong></p>
     *
     * The ingredients list is sanitized to prevent XSS attacks. Allergens
     * are specially marked with a red asterisk and styled in red. The pizza
     * name and price are also sanitized and formatted.
     */
    public function displayInMenu(): string
    {
        // Initialize the output with the pizza name wrapped in <h3> tags.
        $output = '<h4>' . htmlspecialchars($this->name) . '</h4>';

        // Create a string for the ingredients, separated by commas.
        $ingredientsList = [];
        foreach ($this->ingredients as $ingredient) {
            // Escaping the ingredient name to prevent XSS attacks.
            $ingredientName = htmlspecialchars($ingredient['name']);
            // If the ingredient is an allergen, style it with red color and a red asterisk.
            if ($ingredient['isAllergen']) {
                $ingredientsList[] = '<span style="color: var(--secondary);">' . $ingredientName . ' *</span>';
            } else {
                // If it's not an allergen, just append the ingredient name.
                $ingredientsList[] = $ingredientName;
            }
        }

        // Join all the ingredients with a comma and wrap them in <i> tags for styling.
        $output .= '<p><i>' . implode(', ', $ingredientsList) . '</i></p>';

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
        } else {
            parent::__set($property, $value);
        }
    }

    /**
     * @throws Exception
     */
    public static function getById(int $id): Pizza
    {
        return new self($id);
    }

}
