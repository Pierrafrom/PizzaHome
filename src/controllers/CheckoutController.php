<?php

namespace App\controllers;

use App\DB_Connection;
use App\helpers\SessionHelper;
use App\helpers\URL;
use Exception;

/**
 * CheckoutController class handles the checkout process for orders.
 * 
 * It extends from the base Controller class and includes the following methods:
 * 
 * - __construct(string $viewPath): Initializes page title, CSS, and JavaScript files for the checkout page.
 * - checkout(): Processes the checkout form submission and handles order creation.
 * - validateCheckoutData(): Validates the data submitted through the checkout form.
 *
 * @package App\controllers
 */
class CheckoutController extends Controller
{
    /**
     * Constructs the CheckoutController with specified view path.
     * Initializes page title, CSS, and JavaScript files for the checkout page.
     *
     * @param string $viewPath The path to the view file associated with this controller.
     */
    public function __construct(string $viewPath)
    {
        parent::__construct($viewPath); // Call the parent constructor for basic setup
        self::$title = "Checkout"; // Set the title specific to the registration and login pages
        self::$cssFiles = ["form.css"]; // Define CSS files for page styling
        self::$scriptFiles = ["checkout.js"]; // Include JavaScript files for checkout functionality
    }

    /**
     * Processes the checkout form submission.
     * 
     * This method handles the POST request for checkout. It validates the checkout data,
     * creates an order in the database, and processes each item in the shopping cart.
     * On successful order creation, it redirects to the home page. If any errors occur,
     * they are caught and displayed.
     *
     * @throws Exception If there's an error in checkout processing or database operations.
     */
    public function checkout()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $this->validateCheckoutData();

                $fullPhone = filter_input(
                    INPUT_POST,
                    'country-code',
                    FILTER_SANITIZE_FULL_SPECIAL_CHARS
                ) .
                    filter_input(
                        INPUT_POST,
                        'phone',
                        FILTER_SANITIZE_FULL_SPECIAL_CHARS
                    );
                $firstname = filter_input(
                    INPUT_POST,
                    'firstname',
                    FILTER_SANITIZE_FULL_SPECIAL_CHARS
                );
                $lastname = filter_input(
                    INPUT_POST,
                    'lastname',
                    FILTER_SANITIZE_FULL_SPECIAL_CHARS
                );
                $p_streetNumber = filter_input(
                    INPUT_POST,
                    'street-number',
                    FILTER_SANITIZE_NUMBER_INT
                );
                $p_street = filter_input(
                    INPUT_POST,
                    'street',
                    FILTER_SANITIZE_FULL_SPECIAL_CHARS
                );
                $p_city = filter_input(
                    INPUT_POST,
                    'city',
                    FILTER_SANITIZE_FULL_SPECIAL_CHARS
                );
                $p_postalCode = filter_input(
                    INPUT_POST,
                    'postal-code',
                    FILTER_SANITIZE_NUMBER_INT
                );
                $p_latitude = filter_input(
                    INPUT_POST,
                    'latitude',
                    FILTER_SANITIZE_NUMBER_FLOAT,
                    FILTER_FLAG_ALLOW_FRACTION
                );
                $p_longitude = filter_input(
                    INPUT_POST,
                    'longitude',
                    FILTER_SANITIZE_NUMBER_FLOAT,
                    FILTER_FLAG_ALLOW_FRACTION
                );

                $orderId = -1;
                $procedureName = 'CreateOrderForClient';
                $input_parameters = [
                    'p_phone' => $fullPhone,
                    'p_firstname' => strtolower($firstname),
                    'p_lastname' => strtoupper($lastname),
                    'p_streetNumber' => $p_streetNumber,
                    'p_street' => $p_street,
                    'p_city' => $p_city,
                    'p_postalCode' => $p_postalCode,
                    'p_latitude' => $p_latitude,
                    'p_longitude' => $p_longitude
                ];
                $output_parameters = [
                    'p_orderId' => &$orderId
                ];
                DB_Connection::callProcedure($procedureName, $input_parameters, $output_parameters);

                if ($orderId === -1) {
                    throw new Exception("Error creating order.");
                }

                $cart = SessionHelper::getSessionVariable('cart');

                if (!isset($cart)) {
                    throw new Exception("No items in cart.");
                }

                // Stored procedure mapping
                $procedureMapping = [
                    'pizza' => 'AddPizzaToOrder',
                    'soda' => 'AddSodaToOrder',
                    'dessert' => 'AddDessertToOrder',
                    'wine' => 'AddWineToOrder',
                    'cocktail' => 'AddCocktailToOrder'
                ];

                foreach ($cart as $productType => $products) {
                    foreach ($products as $product) {
                        $procedureName = $procedureMapping[$productType];
                        $input_parameters = [
                            'p_orderId' => $orderId,
                            'p_productId' => $product['id'],
                            'p_quantity' => $product['quantity']
                        ];
                        DB_Connection::callProcedure($procedureName, $input_parameters);
                    }
                }

                $_SESSION['cart'] = [];
                URL::redirect("/");
            } catch (Exception $e) {
                echo "Error: " . $e->getMessage();
            }
        }
    }

    /**
     * Validates the data submitted through the checkout form.
     * 
     * This method checks for the presence of all required fields, validates their format,
     * and ensures data integrity. It throws an exception if any validation fails.
     * 
     * @throws Exception If a validation check fails.
     */
    private function validateCheckoutData(): void
    {
        // Validation of required fields
        $requiredFields = ['lastname', 'firstname', 'country-code', 'phone', 'card-number', 'cardExpiryMonth', 'cardExpiryYear', 'cardCVC', 'street-number', 'street', 'city', 'postal-code', 'latitude', 'longitude'];
        foreach ($requiredFields as $field) {
            if (empty($_POST[$field])) {
                throw new Exception("All fields are required.");
            }
        }

        // Validatio of the name format
        if (!preg_match('/^[a-zA-Z\s]+$/', $_POST['lastname']) || !preg_match('/^[a-zA-Z\s]+$/', $_POST['firstname'])) {
            throw new Exception("Invalid name format.");
        }

        // Validation of the country code format
        if (!preg_match('/^\+\d{1,3}$/', $_POST['country-code'])) {
            throw new Exception("Invalid country code.");
        }

        // Validation of the phone number format
        if (!preg_match('/^\d{9,10}$/', $_POST['phone'])) {
            throw new Exception("Invalid phone number.");
        }

        // Validation of the card number format
        if (!preg_match('/^(\d{4}[\s]?){3}\d{4}$/', $_POST['card-number'])) {
            throw new Exception("Invalid card number format.");
        }

        // Validation of the card expiry date and CVC
        if ($_POST['cardExpiryMonth'] < 1 || $_POST['cardExpiryMonth'] > 12 || $_POST['cardExpiryYear'] < (int)date('Y') || $_POST['cardExpiryYear'] > (int)date('Y') + 10) {
            throw new Exception("Invalid expiry date.");
        }

        // Validation of the CVC
        if (!preg_match('/^\d{3}$/', $_POST['cardCVC'])) {
            throw new Exception("Invalid CVC.");
        }

        // Validation of the address
        if (!is_numeric($_POST['street-number']) || !preg_match('/^[a-zA-Z\s]+$/', $_POST['street'])) {
            throw new Exception("Invalid street information.");
        }

        // Validation of the city
        if (!preg_match('/^\d+$/', $_POST['postal-code'])) {
            throw new Exception("Invalid postal code.");
        }

        // Validation of the GPS coordinates
        if (!is_numeric($_POST['latitude']) || !is_numeric($_POST['longitude'])) {
            throw new Exception("Invalid GPS coordinates.");
        }
    }
}
