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

    public int $stock;

    public string $bottleType;

    public static $formFields = [
        'name' => ['type' => 'text', 'placeholder' => 'Name of the Soda', 'required' => true],
        'price' => ['type' => 'number', 'placeholder' => 'Price', 'required' => true],
        'spotlight' => ['type' => 'checkbox', 'required' => true],
        'stock' => ['type' => 'number', 'placeholder' => 'Stock Quantity', 'required' => true]
    ];

    /**
     * Constructs a new Soda instance.
     *
     * @param int|null $id The unique identifier of the soda. If null, an automatic ID is generated.
     * @param string|null $name The name of the soda. Defaults to 'My Soda' if not provided.
     * @param float|null $price The price of the soda. Defaults to 4.0 if not provided.
     * @param bool|null $spotlight Indicates if the soda is a featured item. Defaults to false if not provided.
     */
    public function __construct(
        ?int $id = null,
        ?string $name = null,
        ?float $price = null,
        ?bool $spotlight = null,
        ?int $stock = null,
        ?string $bottleType = null
    ) {
        if (!is_null($id)) {
            $this->id = self::$autoIncrementId++;
            $this->name = $name ?? 'My Soda';
            $this->price = $price ?? 4.0;
            $this->spotlight = $spotlight ?? false;
            $this->stock = $stock ?? 0;
            $this->bottleType = 'CAN';
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
        $sql = "SELECT id, name, price, spotlight, stock, bottleType FROM VIEW_SODA";
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
        // Initialize the output with the soda name wrapped in <h3> tags.
        $output = '<h4>' . htmlspecialchars($this->name) . '</h4>';

        // Append the price information, formatted with the euro sign and strong tags for emphasis.
        $output .= '<p><strong>€' . htmlspecialchars($this->price) . '</strong></p>';

        // Return the formatted string.
        return $output;
    }

    /**
     * Retrieves a Soda instance from the database by its ID.
     *
     * @param int $id The ID of the soda to retrieve.
     *
     * @return object|array The Soda instance retrieved from the database.
     *
     */
    public static function getById(int $id): object|array
    {
        $sql = 'SELECT id, name, price, spotlight, stock, bottleType FROM VIEW_SODA WHERE id = :id';
        $results = DB_Connection::query($sql, ['id' => $id], self::class);

        if (count($results) === 1) {
            return $results[0];
        } else {
            return $results;
        }
    }

    /**
     * Retrieves all Soda instances from the database that are featured items.
     * @return array An array of Soda instances fetched from the database.
     */
    public static function getSpotlightSodas(): array
    {
        $sql = "SELECT id, name, price, spotlight, stock, bottleType FROM VIEW_SODA WHERE spotlight = 1";
        $sodas = DB_Connection::query($sql, [], self::class);

        if (is_object($sodas)) {
            return [$sodas];
        }

        return $sodas;
    }

    public static function generateSpecificSection()
    {
        $bottleTypes = ['BOTTLE', 'CAN'];

        $html = '<div class="form-group">';
        $html .= '<label for="bottleType">Type de Bouteille</label>';
        $html .= '<select id="bottleType" name="bottleType">';

        foreach ($bottleTypes as $type) {
            $html .= '<option value="' . htmlspecialchars($type) . '">' . htmlspecialchars(ucfirst(strtolower($type))) . '</option>';
        }

        $html .= '</select>';
        $html .= '</div>';

        return $html;
    }
}
