<?php

namespace App\models;

use App\DB_Connection;
use InvalidArgumentException;

/**
 * The Soda class represents a type of food item in the application.
 * It extends from the abstract Food class and provides specific attributes and behaviors for soda items.
 */
class Soda extends Food
{
    /**
     * @var int Static counter to automatically generate unique identifiers for new Soda instances.
     */
    private static int $autoIncrementId = 1;

    /**
     * Constructs a new Soda instance.
     *
     * @param int|null $id The unique identifier of the soda. If null, an automatic ID is generated.
     * @param string|null $name The name of the soda. Defaults to 'My Soda' if not provided.
     * @param float|null $price The price of the soda. Defaults to 4.0 if not provided.
     * @param bool|null $spotlight Indicates if the soda is a featured item. Defaults to false if not provided.
     */
    public function __construct(?int $id = null, ?string $name = null, ?float $price = null, ?bool $spotlight = null)
    {
        if (!is_null($id)) {
            $this->id = self::$autoIncrementId++;
            $this->name = $name ?? 'My Soda';
            $this->price = $price ?? 4.0;
            $this->spotlight = $spotlight ?? false;
        }
    }

    /**
     * Returns a string representation of the Soda instance.
     *
     * @return string The string representation of the Soda instance.
     */
    public function __toString(): string
    {
        return "Soda: $this->name ($this->price €)";
    }

    /**
     * Retrieves all Soda instances from the database.
     *
     * @return array An array of Soda instances fetched from the database.
     */
    public static function getAllSodas(): array
    {
        $sql = "SELECT id, name, price, spotlight FROM VIEW_SODA";
        $sodas = DB_Connection::query($sql, [], self::class);

        if (is_object($sodas)) {
            return [$sodas];
        }

        return $sodas;
    }

    /**
     * Displays Soda information formatted for a menu.
     *
     * This function generates a string that includes the soda's name and its price,
     * all formatted for display in a menu context.
     *
     * @return string The formatted menu item display string.
     *
     * @example Output Example:
     *          <h3>Soda Name</h3>
     *          <p><strong>€12.99</strong></p>
     *
     */
    public function displayInMenu(): string
    {
        // Initialize the output with the pizza name wrapped in <h3> tags.
        $output = '<h4>' . htmlspecialchars($this->name) . '</h4>';

        // Append the price information, formatted with the euro sign and strong tags for emphasis.
        $output .= '<p><strong>€' . htmlspecialchars($this->price) . '</strong></p>';

        // Return the formatted string.
        return $output;
    }
}