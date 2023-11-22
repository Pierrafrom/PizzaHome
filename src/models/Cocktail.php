<?php

namespace App\models;

use App\DB_Connection;
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
     * Constructor for the Cocktail class.
     * @param int|null $id The unique identifier of the cocktail. If null, an ID will be automatically generated.
     * @param string|null $name The name of the cocktail. If null, a default value will be used.
     * @param float|null $glassPrice The price for a glass of the cocktail. If null, a default value will be used.
     * @param float|null $alcoholPercentage The alcohol percentage contained in the cocktail. If null, a default value will be used.
     * @param bool|null $spotlight Indicates if the cocktail is featured. If null, a default value will be used.
     *
     * @throws Exception Throws an exception if the cocktail cannot be loaded from the database.
     *
     * @example
     * // To load an existing cocktail from the database:
     * $existingCocktail = new Cocktail(1);
     *
     * // To manually create a new cocktail without loading from the database:
     * $newCocktail = new Cocktail(null, "Margarita", 7.99, 19.99, true);
     */
    public function __construct(?int    $id = null,
                                ?string $name = null,
                                ?float  $price = null,
                                ?bool   $spotlight = null,
                                ?float  $alcoholPercentage = null)
    {
        if (!is_null($id)) {
            $this->id = self::$autoIncrementId++;
            $this->name = $name ?? 'My Cocktail';
            $this->price = $price ?? 12.0;
            $this->spotlight = $spotlight ?? false;
            $this->alcoholPercentage = $alcoholPercentage ?? 12.0;
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
        $sql = "SELECT * FROM DRINK WHERE drinkId = :id AND drinkType = 'COCKTAIL'";
        $cocktailDetails = DB_Connection::query($sql, ['id' => $id]);

        // If the dessert is found, initialize the properties of the object.
        if (!empty($cocktailDetails)) {
            $this->id = (int)$cocktailDetails[0]['id'];
            $this->name = $cocktailDetails[0]['name'];
            $this->price = (float)$cocktailDetails[0]['price'];
            $this->alcoholPercentage = (float)$cocktailDetails[0]['alcoholPercentage'];
            $this->spotlight = (bool)$cocktailDetails[0]['spotlight'];

            // Also load the dessert's ingredients.
            $this->loadIngredients();
        } else {
            // Throw an exception if no cocktail is found with this ID.
            throw new Exception("Cocktail not found with ID $id");
        }
    }

    /**
     * Loads the ingredients of a drink from the database.
     */
    private function loadIngredients(): void
    {
        // Execute a query to get the specific drink ingredients.
        $sql = "SELECT ingredientName, isAllergen FROM VIEW_COCKTAIL_INGREDIENTS WHERE id = :id";
        $ingredients = DB_Connection::query($sql, ['id' => $this->id]);

        // Populate the Drink object's ingredients array.
        foreach ($ingredients as $ingredient) {
            $this->ingredients[] = [
                'name' => $ingredient['ingredientName'],
                'isAllergen' => (bool)$ingredient['isAllergen'],
            ];
        }
    }

    /**
     * Retrieves all drinks from the database and returns them as drink objects.
     * @return array An array containing instances of drink.
     * @throws Exception Throws an exception if the query fails.
     */
    public static function getAllCocktails(): array
    {
        try {
            // Query to get all the distinct cocktail IDs.
            $sql = "SELECT id, name, price, spotlight, alcoholPercentage FROM PIZZERIA.VIEW_COCKTAIL_INGREDIENTS GROUP BY id";
            $cocktails = DB_Connection::query($sql, [], self::class);

            if (is_object($cocktails)) {
                return [$cocktails];
            }

            return $cocktails;
        } catch (\Exception $e) {
            // Handle the exception, log it, or rethrow it as needed.
            throw new \Exception("Error fetching cocktails: " . $e->getMessage());
        }
    }



    /**
     * Retrieves all drinks that are set as spotlight from the database.
     *
     * @return array An array of Drink objects that are in the spotlight.
     * @throws Exception Throws an exception if the database query fails.
     */
    public static function getSpotlightCocktail(): array
    {
        // Define the SQL query to select Drink where the spotlight is true.
        $sql = "SELECT id FROM VIEW_COCKTAIL_INGREDIENTS WHERE spotlight = 1 GROUP BY id";

        // Execute the query and store the results.
        $results = DB_Connection::query($sql);

        // Initialize an array to hold the spotlight drink.
        $spotlightDrink = [];

        // Loop through the results and create a new Drink object for each one.
        foreach ($results as $row) {
            // Each drink is loaded from the database by its ID.
            $spotlightDrink[] = new self((int)$row['id']);
        }

        // Return the array of spotlight Drink objects.
        return $spotlightDrink;
    }



    /**
     * Displays cocktail information formatted for a menu.
     *
     * This function generates a string that includes the cocktails name,
     * and its price, all formatted for display in a menu context.
     *
     * @return string The formatted menu item display string.
     *
     * @example Output Example:
     *          <h4>Cocktail Name</h4>
     *          <p><strong>€12.99</strong></p>
     *
     * The cocktails name and price are sanitized and formatted.
     */
    public function displayInMenu(): string
    {
        // Initialize the output with the drink name wrapped in <h3> tags.
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

}
