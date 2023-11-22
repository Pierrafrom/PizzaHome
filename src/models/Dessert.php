<?php

namespace App\models;

use App\DB_Connection;
use Exception;
use InvalidArgumentException;

/**
 * The Dessert class represents a specific dessert.
 * It manages the creation of desserts either by loading from the database
 * or by manual initialization with provided values.
 */
class Dessert extends Food
{
    /**
     * @var int Static counter to automatically generate unique identifiers.
     */
    private static int $autoIncrementId = 1;

    /**
     * @var array An array of ingredients included in the dessert item.
     */
    private array $ingredients = [];

    /**
     * Constructor for the Dessert class.
     *
     * @param int|null $id The unique identifier of the dessert. If null, an ID will be automatically generated.
     * @param string|null $name The name of the dessert. If null, a default value will be used.
     * @param float|null $price The price of the dessert. If null, a default value will be used.
     * @param bool|null $spotlight Indicates if the dessert is featured. If null, a default value will be used.
     *
     * @throws Exception Throws an exception if the dessert cannot be loaded from the database.
     *
     * @example
     * // To load an existing dessert from the database:
     * $existingDessert = new Dessert(1);
     *
     * // To manually create a new dessert without loading from the database:
     * $newDessert = new Dessert(null, "Chocolate Cake", 5.99, true);
     */
    public function __construct(?int $id = null, ?string $name = null, ?float $price = null, ?bool $spotlight = null)
    {
        // If the ID is provided, load the dessert details from the database.
        if (!is_null($id)) {
            $this->loadFromDatabaseById($id);
        } else {
            // Otherwise, manually initialize if the information is provided.
            // The ID is automatically generated if null is passed.
            $this->id = self::$autoIncrementId++;
            // Use the null coalescence operator to provide default values.
            $this->name = $name ?? 'My Dessert';
            $this->price = $price ?? 5.0; // Adjust the default price as needed.
            $this->spotlight = $spotlight ?? false;
        }
    }

    /**
     * Loads the details of a dessert from the database using its unique identifier.
     *
     * @param int $id The unique identifier of the dessert.
     *
     * @throws InvalidArgumentException If the ID is not numeric.
     * @throws Exception If the dessert is not found in the database.
     */
    private function loadFromDatabaseById(int $id): void
    {
        // First check that the ID is valid.
        if (!is_numeric($id)) {
            throw new InvalidArgumentException("ID must be numeric");
        }

        // Retrieve the dessert details from the database.
        $sql = "SELECT id, name, price, spotlight FROM VIEW_DESSERT_INGREDIENTS WHERE id = :id GROUP BY id";
        $dessertDetails = DB_Connection::query($sql, ['id' => $id]);

        // If the dessert is found, initialize the properties of the object.
        if (!empty($dessertDetails)) {
            $this->id = (int)$dessertDetails[0]['id'];
            $this->name = $dessertDetails[0]['name'];
            $this->price = (float)$dessertDetails[0]['price'];
            $this->spotlight = (bool)$dessertDetails[0]['spotlight'];

            // Also load the dessert's ingredients.
            $this->loadIngredients();
        } else {
            // Throw an exception if no dessert is found with this ID.
            throw new Exception("Dessert not found with ID $id");
        }
    }



    /**
     * Loads the ingredients of a dessert from the database.
     */
    private function loadIngredients(): void
    {
        // Execute a query to get the specific dessert's ingredients.
        $sql = "SELECT ingredientName, isAllergen FROM VIEW_DESSERT_INGREDIENTS WHERE id = :id";
        $ingredients = DB_Connection::query($sql, ['id' => $this->id]);

        // Populate the Dessert object's ingredients array.
        foreach ($ingredients as $ingredient) {
            $this->ingredients[] = [
                'name' => $ingredient['ingredientName'],
                'isAllergen' => (bool)$ingredient['isAllergen'],
            ];
        }
    }



    /**
     * Retrieves all desserts from the database and returns them as Dessert objects.
     *
     * @return array An array containing instances of Dessert.
     * @throws Exception Throws an exception if the query fails.
     */
    public static function getAllDesserts(): array
    {
        // Query to get all the distinct dessert IDs.
        $sql = "SELECT DISTINCT id FROM VIEW_DESSERT_INGREDIENTS";
        $dessertIds = DB_Connection::query($sql);

        $dessert = [];
        // For each ID found, create a new Dessert instance.
        foreach ($dessertIds as $row) {
            // Create a new Dessert instance and load it from the database.
            $dessert[] = new self($row['id']);
        }

        return $dessert;
    }

    /**
     * Retrieves all desserts that are set as spotlight from the database.
     *
     * @return array An array of Dessert objects that are in the spotlight.
     * @throws Exception Throws an exception if the database query fails.
     */
    public static function getSpotlightDessert(): array
    {
        // Define the SQL query to select dessert where the spotlight is true.
        $sql = "SELECT id FROM VIEW_DESSERT_INGREDIENTS WHERE spotlight = 1 GROUP BY id";

        // Execute the query and store the results.
        $results = DB_Connection::query($sql);

        // Initialize an array to hold the spotlight dessert.
        $spotlightDessert = [];

        // Loop through the results and create a new Dessert object for each one.
        foreach ($results as $row) {
            // Each dessert is loaded from the database by its ID.
            $spotlightDessert[] = new self((int)$row['id']);
        }

        // Return the array of spotlight Dessert objects.
        return $spotlightDessert;
    }

    /**
     * Displays dessert information formatted for a menu.
     *
     * This function generates a string that includes the dessert's name,
     * and its price, all formatted for display in a menu context.
     *
     * @return string The formatted menu item display string.
     *
     * @example Output Example:
     *          <h4>Dessert Name</h4>
     *          <p><strong>€5.99</strong></p>
     *
     * The dessert name and price are sanitized and formatted.
     */
    public function displayInMenu(): string
    {
        // Initialize the output with the dessert name wrapped in <h3> tags.
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

    // Additional methods specific to the Dessert class can be added as needed.

}
