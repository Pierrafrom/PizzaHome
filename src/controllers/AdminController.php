<?php

namespace App\controllers;

use App\DB_Connection;
use App\helpers\SessionHelper;
use App\helpers\URL;
use App\models\Cocktail;
use App\models\Dessert;
use App\models\Ingredient;
use App\models\Pizza;
use App\models\Soda;
use App\models\Wine;
use Exception;
use InvalidArgumentException;

/**
 * The AdminController class manages the administrative interface for the application.
 * It extends from the base Controller class and provides functionality to handle
 * administrative tasks such as managing stock and menu items.
 *
 * Responsibilities include:
 * - Rendering the admin page with appropriate data.
 * - Loading various sections of the admin page like stock and menu items.
 * - Updating stock quantities of products.
 * - Deleting products from the database.
 * - Updating the spotlight status of products.
 *
 * @package App\controllers
 */
class AdminController extends Controller
{
    /**
     * Constructs the AdminController with specified view path.
     * It initializes the title, CSS, JS, and module files required for the admin view.
     *
     * @param string $viewPath The path to the view file associated with this controller.
     */
    public function __construct(string $viewPath)
    {
        parent::__construct($viewPath);
        self::$title = 'Admin';
        self::$cssFiles = ["banner.css", "admin.css", "table.css"];
        self::$scriptFiles = ["tabs.js"];
        self::$scriptLinkFiles = ["https://cdn.jsdelivr.net/npm/apexcharts"];
        self::$moduleFiles = ["admin.js", "charts.js"];
    }

    /**
     * Loads the admin page. It checks if the user is an admin before proceeding.
     * In case of an exception, it displays appropriate error messages based on the environment.
     *
     * @throws Exception If there's an error in loading the page.
     */
    public function loadPage(): void
    {
        if (!SessionHelper::is_admin()) {
            URL::redirect("/registration?redirect=" . urlencode($_SERVER['REQUEST_URI']) . "#login");
        }
        try {
            $this->viewData = [
                'ingredientSection' => $this->loadStockSection(Ingredient::getAllIngredients(), 'Ingredient'),
                'winesSection' => $this->loadStockSection(Wine::getWinesByColor(), 'Wine'),
                'sodasSection' => $this->loadStockSection(Soda::getAllSodas(), 'Soda'),
                'pizzasSection' => $this->loadMenuSection(Pizza::getAllPizzas(), 'Pizza'),
                'dessertsSection' => $this->loadMenuSection(Dessert::getAllDesserts(), 'Dessert'),
                'cocktailsSection' => $this->loadMenuSection(Cocktail::getAllCocktails(), 'Cocktail')
            ];

            parent::loadPage();
        } catch (Exception $e) {
            if ($_ENV['ENVIRONMENT'] == 'development') {
                echo '<p>' . $e->getMessage() . '</p>';
            } else {
                echo '<p>An error occurred while loading the admin page.</p>';
            }
        }
    }

    /**
     * Generates HTML for the stock section of the admin page.
     * It creates a table of items with actions to edit or delete them.
     *
     * @param array $items Array of item objects to display in the stock section.
     * @param string $itemName Name of the item type (e.g., 'Ingredient', 'Wine').
     * @return string HTML content for the stock section.
     */
    private function loadStockSection(array $items, string $itemName): string
    {
        $stockProperty = 'stock';
        try {
            $output = '<table><thead><tr>';
            $output .= '<th>' . $itemName . '</th>';
            $output .= '<th>Stock</th>';
            $output .= '<th>Edit</th>';
            if ($itemName === 'Wine' || $itemName === 'Soda') {
                $output .= '<th>Spotlight</th>';
            }
            $output .= '<th>Delete</th>';
            $output .= '</tr></thead>';
            $output .= '<tbody>';
            foreach ($items as $item) {
                $output .= '<tr>';
                $output .= '<td>' . $item->name . '</td>';
                try {
                    $output .= '<td>' . $item->$stockProperty . ' ' . $item->unit . '</td>';
                } catch (InvalidArgumentException) {
                    $output .= '<td>' . $item->$stockProperty . ' ' . $item->bottleType . '</td>';
                }
                $output .= '<td style="min-width: 150px;">';
                $output .= '<button type="button" class="btn-error decrement-button">-</button>';
                $output .= '<input type="number" class="' . 'stock-quantity' . '" min="0"
                            data-stock-id="' . $item->id . '"
                            data-stock-type="' . $itemName . '"
                            value="' . $item->$stockProperty . '">';
                $output .= '<button type="button" class="btn-primary increment-button">+</button>';
                $output .= '<button class="btn-primary confirm-quantity hide">Confirm</button>';
                $output .= '</td>';
                if ($itemName === 'Wine' || $itemName === 'Soda') {
                    $output .= '<td><input type="checkbox" ' .
                        'class="spotlight-checkbox"
                    data-product-id="' . $item->id . '"
                    data-product-type="' . $itemName . '"
                    ' . ($item->spotlight ? 'checked' : '') . '></td>';
                }
                $output .= '<td style="min-width: 150px;">';
                $output .= '<button data-product-id="' . $item->id . '"
                        data-product-type="' . $itemName . '"
                        class="btn-error btn-remove">Delete</button>';
                $output .= '</tr>';
            }
            $output .= '</tbody></table>';
            return $output;
        } catch (Exception $e) {
            if ($_ENV['ENVIRONMENT'] == 'development') {
                return '<p>' . $e->getMessage() . '</p>';
            } else {
                return '<p>An error occurred while loading the ' . $itemName . ' section.</p>';
            }
        }
    }

    /**
     * Generates HTML for the menu section of the admin page.
     * Similar to the stock section, it creates a table of menu items with options to edit or delete.
     *
     * @param array $items Array of item objects to display in the menu section.
     * @param string $itemType Type of the menu item (e.g., 'Pizza', 'Dessert').
     * @return string HTML content for the menu section.
     */
    private function loadMenuSection(array $items, string $itemType): string
    {
        try {
            $output = '<table><thead><tr>';
            $output .= '<th>' . $itemType . '</th>';
            $output .= '<th>Price</th>';
            $output .= '<th>Spotlight</th>';
            $output .= '<th>Delete</th>';
            $output .= '</tr></thead>';
            $output .= '<tbody>';
            foreach ($items as $item) {
                $output .= '<tr>';
                $output .= '<td>' . $item->name . '</td>';
                $output .= '<td>$' . $item->price . '</td>';
                $output .= '<td><input type="checkbox" class="spotlight-checkbox"
                            data-product-id="' . $item->id . '"
                            data-product-type="' . $itemType . '"
                            ' . ($item->spotlight ? 'checked' : '') . '></td>';
                $output .= '<td style="min-width: 150px;">';
                $output .= '<button data-product-id="' . $item->id . '"
                        data-product-type="' . $itemType . '"
                        class="btn-error btn-remove">Delete</button>';
                $output .= '</td>';
                $output .= '</tr>';
            }
            $output .= '</tbody></table>';
            return $output;
        } catch (Exception $e) {
            if ($_ENV['ENVIRONMENT'] == 'development') {
                return '<p>' . $e->getMessage() . '</p>';
            } else {
                return '<p>An error occurred while loading the ' . $itemType . ' section.</p>';
            }
        }
    }

    /**
     * Updates the stock quantity of a product in the database.
     *
     * @param int $productId The ID of the product to update.
     * @param string $productType The type of the product (e.g., 'Ingredient', 'Wine').
     * @param float $newQuantity The new quantity to set for the product.
     * @return bool True if the update was successful, false otherwise.
     * @throws InvalidArgumentException If the product type is invalid.
     */
    public static function updateStockQuantity(int $productId, string $productType, float $newQuantity): bool
    {
        switch ($productType) {
            case 'Ingredient':
                DB_Connection::callProcedure('UpdateIngredientStock', ['ingredientID' => $productId, 'newQuantity' => $newQuantity]);
                return true;
            case 'Wine':
                DB_Connection::callProcedure('UpdateWineStock', ['wineID' => $productId, 'newQuantity' => $newQuantity]);
                return true;
            case 'Soda':
                DB_Connection::callProcedure('UpdateSodaStock', ['sodaID' => $productId, 'newQuantity' => $newQuantity]);
                return true;
            default:
                throw new InvalidArgumentException('Invalid product type.');
        };
    }

    /**
     * Deletes an item (product) from the database based on its ID and type.
     *
     * @param int $id The ID of the item to delete.
     * @param string $type The type of the item (e.g., 'Ingredient', 'Pizza').
     * @return bool|array True if the deletion was successful, or an array with error details.
     * @throws Exception If there's an error during deletion.
     */
    public static function deleteObject(int $id, string $type): bool|array
    {
        try {
            $params = [
                'itemId' => $id,
                'itemType' => $type
            ];
            DB_Connection::callProcedure('DeleteItem', $params);
            return true;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Updates the spotlight status of a product.
     *
     * @param int $id The ID of the product.
     * @param string $type The type of the product.
     * @param bool $spotlight The new spotlight status to be set.
     * @return bool True if the update was successful.
     * @throws Exception If there's an error during the update.
     */
    public static function updateSpotlight(int $id, string $type, bool $spotlight): bool
    {
        try {
            $params = [
                'itemId' => $id,
                'itemType' => $type,
            ];
            DB_Connection::callProcedure('ToggleSpotlight', $params);
            return true;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
}
