<?php

namespace App\models;

use InvalidArgumentException;
use Exception;

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

    /**
     * Constructs a lowercase, URL-friendly version of the wine's name.
     *
     * This method takes the wine's name, replaces all spaces with hyphens,
     * and converts the entire string to lowercase. It also removes any content
     * within parentheses and apostrophes. This is useful for creating
     * clean, readable URLs or file names that require lowercase characters
     * and no spaces.
     *
     * @return string The wine's name in lowercase with spaces replaced by hyphens
     *                and apostrophes removed.
     */
    public function getImageName(): string
    {
        // First, remove all content within parentheses, including the parentheses themselves
        $nameWithoutParentheses = preg_replace('/\s*\([^)]*\)/', '', $this->name);

        // Remove apostrophes
        $nameWithoutApostrophes = str_replace("'", '-', $nameWithoutParentheses);

        // Then replace spaces with hyphens and convert to lowercase
        return strtolower(str_replace(' ', '-', $nameWithoutApostrophes));
    }

    /**
     * Generates a string containing a comma-separated list of ingredient names
     * for the food item, enclosed within a paragraph HTML element.
     *
     * The method checks if the `$ingredients` property is defined and an array.
     * If not, it throws an exception. Otherwise, it maps over the `$ingredients` array,
     * extracting the 'name' of each ingredient and concatenating these names into a
     * single string separated by commas. The resultant string is sanitized using
     * `htmlspecialchars()` to prevent XSS attacks when displayed in HTML.
     *
     * @return string Returns a string of ingredient names wrapped in a paragraph tag.
     *                The special characters in the ingredient names are converted to HTML entities.
     * @throws Exception If `$ingredients` is not defined or is not an array.
     */
    public function getDescription(): string
    {
        if (!isset($this->ingredients) || !is_array($this->ingredients)) {
            return '';
            //throw new Exception("Ingredients not defined or not an array.");
        }

        return '<p>' . implode(', ', $this->ingredients) . '</p>';
    }

    /**
     * Generates a string containing a comma-separated list of ingredient names
     * for the food item, enclosed within a paragraph HTML element.
     * The method checks if the `$ingredients` property is defined and an array.
     * If not, it throws an exception. Otherwise, it maps over the `$ingredients` array,
     * extracting the 'name' of each ingredient and concatenating these names into a
     * single string separated by commas. The resultant string is sanitized using
     * `htmlspecialchars()` to prevent XSS attacks when displayed in HTML.
     * 
     * @return string Returns a string of ingredient names wrapped in a paragraph tag.
     *               The special characters in the ingredient names are converted to HTML entities.
     * @throws Exception If `$ingredients` is not defined or is not an array.
     */
    public static function generateSpecificSection()
    {
        // si la classe n'est pas une pizza un dessert ou un cocktail exception
        if (!in_array(static::class, [Pizza::class, Dessert::class, Cocktail::class])) {
            throw new Exception('Invalid class');
        }
        $ingredientsList = Ingredient::getAllIngredients();

        $html = '<div class="divided-group toDuplicate">
                    <div class="form-group">
                        <label for="ingredient">Ingredient</label>
                        <button type="button" class="delete-btn">X</button>
                        <select id="ingredient" name="ingredient-1">
                            <option value="-1">Select an Ingredient</option>';
        foreach ($ingredientsList as $ingredient) {
            $html .= '<option value="' . htmlspecialchars($ingredient->id) . '">' . htmlspecialchars($ingredient->name)
                . ' (' . htmlspecialchars($ingredient->unit) . ') ' . '</option>';
        }
        $html .= '</select>
                </div>
                <div class="form-group">
                    <label for="quantity">Quantity</label>
                    <input type="number" id="quantity" name="quantity-1" placeholder="Quantity" required>
                </div>
            </div>';
        $html .= '<div class="divided-group add-section">
                <button class="btn-secondary duplicate-btn" type="button">Add Ingredient</button>';
        $html .= '<a href="/creation?class=App%5Cmodels%5CIngredient&redirect=' . urlencode($_SERVER['REQUEST_URI']) . '" class="btn-primary">New Ingredient</a>';
        $html .= '</div>';

        return $html;
    }
}
