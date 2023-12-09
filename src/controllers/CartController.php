<?php

namespace App\controllers;

use App\helpers\SessionHelper;
use App\models\Cocktail;
use App\models\Dessert;
use App\models\Pizza;
use App\models\Soda;
use App\models\Wine;
use Exception;

/**
 * Class CartController
 *
 * The CartController class is responsible for managing the shopping cart functionality within the application.
 * It extends from the base Controller class and includes methods to load the cart page, generate cart views,
 * manage cart items, and add products to the shopping cart.
 *
 * This class interacts with various models such as Pizza, Soda, Dessert, Wine, and Cocktail to retrieve
 * product information and manages these products within the user's shopping cart. It uses the SessionHelper
 * to handle session data related to the shopping cart.
 *
 * Methods:
 *  - loadPage(): Overrides the parent Controller class's loadPage method to render the cart view.
 *  - generateCartView(): Generates the HTML view for the shopping cart.
 *  - generateCartWithObjects(): Transforms the cart session data into an array of product objects.
 *  - addProductToCart(int $productId, string $productType, int $productQuantity): Static method to add a product to the cart.
 *
 * Usage:
 * The class is used to control the interactions of the shopping cart, such as adding items to the cart,
 * displaying the cart, and managing the items within the cart. It is a key component of the e-commerce functionality of the application.
 *
 * @package App\controllers
 */
class CartController extends Controller
{
    /**
     * CartController constructor.
     *
     * Initializes the CartController instance. Sets the view path for the cart, the default title for the cart page,
     * and specifies the CSS files to be used specifically for the cart view.
     *
     * The constructor calls the parent constructor of the Controller class to handle basic initialization,
     * such as setting the view path. It then sets the page title specific to the CartController to 'Cart'
     * and specifies additional CSS files that are relevant to the cart view, like 'banner.css'.
     *
     * @param string $viewPath The base path for the view files specific to the CartController.
     */
    public function __construct(string $viewPath)
    {
        parent::__construct($viewPath); // Call the parent constructor to set the view path
        self::$title = 'Cart'; // Set the title for the cart page
        self::$cssFiles = ["banner.css"]; // Specify CSS files for the cart view
        self::$scriptFiles = ["quantity-controls.js"]; // Specify JS files for the cart view
        self::$moduleFiles = ["cart.js"]; // Specify JS files for the cart view
    }

    /**
     * Load and render the cart page.
     *
     * This method overrides the loadPage method of the parent Controller class.
     * It calls the parent loadPage method to render the cart view.
     *
     * @return void
     * @throws Exception
     */
    public function loadPage(): void
    {
        $this->viewData = [
            'cart' => $this->generateCartView(),
        ];
        parent::loadPage();
    }

    /**
     * Generates the HTML view for the shopping cart.
     *
     * This method constructs an HTML table representation of the shopping cart. It first calls
     * `generateCartWithObjects` to transform session cart data into a structured array of product objects.
     * Each product in the cart is represented as a row in the table, displaying product details such as
     * name, quantity, price, subtotal, and a removal link.
     *
     * If the cart is empty, a message indicating that the cart is empty is returned. In case of any
     * exceptions during the process, an error message is displayed. For development environments, the
     * error message includes detailed exception information.
     *
     * @return string The HTML content representing the cart view.
     * @throws Exception If an error occurs during the generation of the cart view.
     * @see generateCartWithObjects For details on how cart data is structured.
     */
    private function generateCartView(): string
    {
        $cart = $this->generateCartWithObjects();
        if (empty($cart)) {
            return '<p>Your cart is empty.</p>';
        }
        $output = '';
        try {
            $output .= '<table><thead><tr>';
            $output .= '<th>Product</th>';
            $output .= '<th>Quantity</th>';
            $output .= '<th>Price</th>';
            $output .= '<th>Subtotal</th>';
            $output .= '<th>Remove</th>';
            $output .= '</tr></thead>';
            $output .= '<tbody>';
            foreach ($cart as $productType => $products) {
                foreach ($products as $productId => $product) {
                    $productObject = $product['product'];
                    $productQuantity = intval($product['quantity']);
                    $productPrice = $productObject->price;
                    $productSubtotal = $productPrice * $productQuantity;
                    $output .= '<tr>';
                    $output .= '<td>' . $productObject->name . '</td>';
                    $output .= '<td>';
                    $output .= '<input type="number" class="product-quantity" min="0"
                                data-product-id="' . $productId . '"
                                data-product-type="' . $productType . '"
                                value="' . $productQuantity . '">';
                    $output .= '<button class="btn-primary confirm-quantity hide">Confirm</button>';
                    $output .= '</td>';
                    $output .= '<td>' . $productPrice . '</td>';
                    $output .= '<td>' . $productSubtotal . '</td>';
                    $output .= '<td><button data-product-id="' . $productObject->id . '"
                                    data-product-type="' . $productType . '"
                                    class="btn-error cart-remove">Remove</button></td>';
                    $output .= '</tr>';
                }
            }
            $output .= '</tbody></table>';
        } catch (Exception $e) {
            $output = '<p>An error occurred while retrieving the cart</p>';
            if ($_ENV['ENVIRONMENT'] == 'development') {
                $output .= '<p>' . $e->getMessage() . '</p>';
            }
        }
        return $output;
    }

    /**
     * Transforms the session cart data into an array of product objects.
     *
     * This method retrieves the current state of the shopping cart from the session, using the SessionHelper.
     * It then iterates over each item in the cart, categorizing products by their type (such as pizza, soda, dessert, wine, cocktail).
     * For each product, it fetches the full product object from the corresponding model based on the product ID and combines
     * this with the quantity information from the cart. This results in a structured array where each product type is a key,
     * and the value is an associative array of product details including the product object and quantity.
     *
     * If the cart is empty or not set in the session, an empty array is returned. The method may throw exceptions
     * if there is an error in fetching product details from the models or from the session. In a development environment,
     * detailed exception messages are thrown, while in other environments, a generic error message is used.
     *
     * @return array An associative array representing the cart with full product objects and quantities.
     * @throws Exception If an error occurs during the processing of cart data.
     */
    private function generateCartWithObjects(): array
    {
        $cart = SessionHelper::getSessionVariable('cart');
        if (is_null($cart)) {
            return [];
        }
        $cartWithObjects = [];
        try {
            foreach ($cart as $productType => $products) {
                foreach ($products as $product) {
                    switch ($productType) {
                        case 'pizza':
                            $cartWithObjects[$productType][$product['id']] = [
                                'product' => Pizza::getById($product['id']),
                                'quantity' => $product['quantity']
                            ];
                            break;
                        case 'soda':
                            $cartWithObjects[$productType][$product['id']] = [
                                'product' => Soda::getById($product['id']),
                                'quantity' => $product['quantity']
                            ];
                            break;
                        case 'dessert':
                            $cartWithObjects[$productType][$product['id']] = [
                                'product' => Dessert::getById($product['id']),
                                'quantity' => $product['quantity']
                            ];
                            break;
                        case 'wine':
                            $cartWithObjects[$productType][$product['id']] = [
                                'product' => Wine::getById($product['id']),
                                'quantity' => $product['quantity']
                            ];
                            break;
                        case 'cocktail':
                            $cartWithObjects[$productType][$product['id']] = [
                                'product' => Cocktail::getById($product['id']),
                                'quantity' => $product['quantity']
                            ];
                            break;
                    }
                }
            }
        } catch (Exception $e) {
            if ($_ENV['ENVIRONMENT'] == 'development') {
                throw new Exception($e->getMessage());
            } else {
                throw new Exception('An error occurred while retrieving the cart.');

            }
        }
        return $cartWithObjects;
    }

    /**
     * Adds a product to the shopping cart.
     *
     * This static method adds a specific product, identified by its ID and type, to the shopping cart stored in the session.
     * The cart is an associative array within the session, structured by product types (e.g., 'pizza', 'soda'). Each product
     * type key maps to an array of products, where each product is represented by another associative array containing
     * 'id' and 'quantity' keys. If the cart or the specific product type does not exist in the session, it's initialized.
     * The method checks if the product is already in the cart, updating its quantity if found, or adds it as a new entry if not.
     *
     * Shopping Cart Structure:
     * $_SESSION['cart'] = [
     *     'pizza' => [
     *         ['id' => int, 'quantity' => int],
     *         // more pizzas...
     *     ],
     *     'soda' => [
     *         ['id' => int, 'quantity' => int],
     *         // more sodas...
     *     ],
     *     // other product types like 'dessert', 'wine', 'cocktail', etc.
     * ];
     *
     * @param int $productId The ID of the product to add.
     * @param string $productType The type of the product (e.g., 'pizza', 'soda').
     * @param int $productQuantity The quantity of the product to add.
     * @return bool Returns true indicating successful addition of the product to the cart.
     * @throws Exception If there is an issue initializing or modifying the session.
     */
    public static function addProductToCart(int $productId, string $productType, int $productQuantity): bool
    {
        SessionHelper::initSession(); // Initialize session variables

        // Initialize the cart and the product type in the cart if not already set
        $_SESSION['cart'][$productType] = $_SESSION['cart'][$productType] ?? [];

        // Find the product in the cart
        $productFound = false;
        foreach ($_SESSION['cart'][$productType] as &$product) {
            if ($product['id'] === $productId) {
                // Update the quantity of the existing product
                $product['quantity'] += $productQuantity;
                $productFound = true;
                break;
            }
        }

        // Add the product if it's not found
        if (!$productFound) {
            $_SESSION['cart'][$productType][] = ['id' => $productId, 'quantity' => $productQuantity];
        }

        return true; // Indicate that the product was added successfully
    }

    /**
     * Removes a product from the shopping cart.
     *
     * This method removes a specific product, identified by its ID and type, from the shopping cart stored in the session.
     * It searches for the product in the cart and removes it entirely, regardless of the quantity.
     *
     * @param int $productId The ID of the product to remove.
     * @param string $productType The type of the product (e.g., 'pizza', 'soda').
     * @return bool True if the product was successfully removed, false otherwise.
     * @throws Exception If there is an issue initializing or modifying the session or if the product is not found in the cart.
     */
    public static function removeProductFromCart(int $productId, string $productType): bool
    {
        SessionHelper::initSession(); // Initialize session variables

        // Check if the cart or the specific product type exists in the session
        if (!isset($_SESSION['cart']) || !isset($_SESSION['cart'][$productType])) {
            throw new Exception('The cart or product type does not exist in the session.');
        }

        // Find and remove the product from the cart
        $cart = &$_SESSION['cart'][$productType]; // Use a reference to modify the original array
        foreach ($cart as $key => $product) {
            if ($product['id'] === $productId) {
                unset($cart[$key]); // Remove the product from the cart
                return true; // Product found and removed, return true
            }
        }

        // If the product was not found in the cart, throw an exception
        throw new Exception('The product was not found in the cart.');
    }

    /**
     * Updates the quantity of a product in the shopping cart.
     *
     * This function allows you to update the quantity of a specific product in the shopping cart.
     *
     * @param int $productId The ID of the product to update.
     * @param string $productType The type of the product (e.g., 'pizza', 'soda').
     * @param int $newQuantity The new quantity to set for the product.
     *
     * @return bool True if the cart was successfully updated, false otherwise.
     * @throws Exception If there is an issue initializing or modifying the session,
     *                   or if the product is not found in the cart.
     */
    public static function updateProductQuantityInCart(int $productId, string $productType, int $newQuantity): bool
    {
        SessionHelper::initSession(); // Initialize session variables

        // Check if the cart or the specific product type exists in the session
        if (!isset($_SESSION['cart']) || !isset($_SESSION['cart'][$productType])) {
            throw new Exception('The cart or product type does not exist in the session.');
        }

        // Find and update the product quantity in the cart
        $cart = &$_SESSION['cart'][$productType]; // Use a reference to modify the original array
        foreach ($cart as &$product) {
            if ($product['id'] === $productId) {
                // Update the quantity of the existing product
                $product['quantity'] = $newQuantity;
                return true; // Product quantity updated successfully, return true
            }
        }

        // If the product was not found in the cart, throw an exception
        throw new Exception('The product was not found in the cart.');
    }
}
