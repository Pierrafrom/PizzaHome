<?php

namespace App\controllers;

use App\helpers\DB_Helper;
use Exception;
use JetBrains\PhpStorm\NoReturn;
use App\DB_Connection;
use App\models\PizzaCustom;

/**
 * ApiController class handles API requests for various functionalities like user authentication, 
 * managing products in the shopping cart, and retrieving statistical data.
 * 
 * Methods included in this class:
 * 
 * - isPostRequest(): bool - Checks if the current request is a POST request.
 * - sendResponse(mixed $data, int $statusCode = 200): void - Sends a JSON response with the given data and status code.
 * - verifyPassword(): void - Verifies user password for authentication.
 * - checkEmailExists(): void - Checks if an email exists in the database.
 * - addProductToCart(): void - Adds a product to the shopping cart.
 * - removeProductFromCart(): void - Removes a product from the shopping cart.
 * - updateProductQuantity(): void - Updates the quantity of a product in the shopping cart.
 * - updatestockQuantity(): void - Updates the stock quantity of a product.
 * - addCustomPizzaToCart(): void - Adds a custom pizza to the shopping cart.
 * - deleteItem(): void - Deletes an item from the database.
 * - getTopProducts(): void - Retrieves the top-selling products for the last 30 days.
 * - getSalesByMonth(): void - Fetches sales data grouped by month.
 * - getPizzaStats(): void - Retrieves statistical data about pizzas.
 * - updateSpotlight(): void - Updates the spotlight status of a specified product.
 *
 * @package App\controllers
 */
class ApiController
{

    /**
     * Checks if the current request is a POST request.
     *
     * This function checks the server's request method and ensures that it is a POST request.
     * If the request method is not POST, it sends a response with an error message and a 405 status code.
     *
     * @return bool Returns true if the request method is POST.
     */
    public function isPostRequest(): bool
    {
        // Check if the request method is not POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            // Send an error response if the method is not POST
            $this->sendResponse(['error' => 'Invalid request method. Only POST is allowed.'], 405);
        }
        // Return true indicating it is a POST request
        return true;
    }

    /**
     * Sends a JSON response with the given data and status code.
     *
     * This function sets the content type header to 'application/json', sets the HTTP response code,
     * and outputs the given data as a JSON-encoded string. It then terminates the script execution.
     *
     * @param mixed $data The data to be sent in the response, typically an array or object.
     * @param int $statusCode The HTTP status code for the response. Defaults to 200 (OK).
     *
     * @return void
     *
     * @throws NoReturn This function terminates script execution using 'exit'.
     */
    #[NoReturn] public function sendResponse(mixed $data, int $statusCode = 200): void
    {
        // Set the content type to JSON
        header('Content-Type: application/json');
        // Set the HTTP response code
        http_response_code($statusCode);
        // Encode the data to JSON and output it
        echo json_encode($data);
        // Terminate the script execution
        exit;
    }

    /**
     * Verifies user password.
     *
     * This method handles a request to verify a user's email and password.
     * It reads JSON data from the PHP input stream, checks the credentials, and
     * returns a JSON response indicating success or failure.
     * @throws Exception
     */
    public function verifyPassword(): void
    {
        if ($this->isPostRequest()) {
            $postData = json_decode(file_get_contents('php://input'), true);
            $email = $postData['email'] ?? '';
            $password = $postData['password'] ?? '';

            // check if the credentials are correct for a user
            try {
                DB_Helper::verifyCredentials($email, $password);
                $isCorrect = true;
            } catch (Exception) {
                $isCorrect = false;
            }

            // check if the credentials are correct for an admin
            if (!$isCorrect) {
                try {
                    DB_Helper::adminLogin($email, $password);
                    $isCorrect = true;
                } catch (Exception) {
                    $isCorrect = false;
                }
            }

            if ($isCorrect) {
                $this->sendResponse(['success' => true]);
            } else {
                $this->sendResponse(['success' => false, 'message' => 'Incorrect credentials']);
            }
        }
    }

    /**
     * Checks if an email exists in the database.
     *
     * This function is designed to be called as part of an API endpoint. It reads
     * JSON data from the request body, which should contain an email address. It then
     * checks if this email exists in the database by calling a separate helper function.
     * The result of this check is returned as a JSON response. In case of an error
     * during the procedure call, the error message is returned in the JSON response.
     *
     * @return void Outputs a JSON response.
     */
    public
    function checkEmailExists(): void
    {
        if ($this->isPostRequest()) {
            $postData = json_decode(file_get_contents('php://input'), true);

            // Retrieve email from the decoded data
            $email = $postData['email'] ?? '';

            $exists = false;
            $errorMessage = null;

            // Attempt to call the procedure for the email check
            try {
                $exists = DB_Helper::checkEmailExists($email);
            } catch (Exception $e) {
                // Store the error message from the exception
                $errorMessage = $e->getMessage();
            }

            // Construct and send a JSON response based on the result or error
            if ($errorMessage === null) {
                if ($exists) {
                    $this->sendResponse(['success' => true]);
                } else {
                    $this->sendResponse(['success' => false, 'message' => 'Email does not exist']);
                }
            } else {
                // Respond with the error message
                $this->sendResponse(['success' => false, 'message' => $errorMessage]);
            }
        }
    }

    /**
     * Adds a product to the shopping cart.
     *
     * This function first checks if the current request is a POST request by calling `isPostRequest()`.
     * If it is, it retrieves the product ID and product type from the POST data.
     * It then calls a method to add the specified product to the cart and sends a response with the result.
     *
     * @return void
     * @throws Exception When the product ID or type is not set.
     */
    public function addProductToCart(): void
    {
        // Check if the current request is a POST request
        if ($this->isPostRequest()) {
            // Retrieve the product ID from POST data, default to null if not set
            $productId = $_POST['productId'] ?? null;
            // Retrieve the product type from POST data, default to null if not set
            $productType = $_POST['productType'] ?? null;
            // Retrieve the product quantity from POST data
            $productQuantity = $_POST['productQuantity'];
            if ($productQuantity < 1) {
                $this->sendResponse(['success' => false, 'message' => 'Invalid product quantity']);
            } else {
                // Call the method to add the product to the cart and store the result
                $result = CartController::addProductToCart($productId, $productType, $productQuantity);
                // Send the response back with the result
                $this->sendResponse($result);
            }
        }
    }

    /**
     * Removes a product from the shopping cart.
     *
     * This function first checks if the current request is a POST request by calling `isPostRequest()`.
     * If it is, it retrieves the product ID and product type from the JSON request body.
     * It then calls a method to remove the specified product from the cart and sends a response with the result.
     *
     * @return void
     */
    public function removeProductFromCart(): void
    {
        // Check if the current request is a POST request
        if ($this->isPostRequest()) {
            // Read the JSON data from the request body
            $jsonRequestBody = file_get_contents('php://input');
            $requestData = json_decode($jsonRequestBody, true);

            // Check if both product ID and type are provided in the JSON data
            $productId = $requestData['productId'] ?? null;
            $productType = $requestData['productType'] ?? null;

            if ($productId !== null && $productType !== null) {
                try {
                    // Call the method to remove the product from the cart
                    $result = CartController::removeProductFromCart($productId, $productType);
                    // Send the response back with the result
                    $this->sendResponse(['success' => $result]);
                } catch (Exception $e) {
                    // Handle any exceptions by sending an error response
                    $this->sendResponse(['success' => false, 'message' => $e->getMessage()]);
                }
            } else {
                // Send an error response if product ID or type is missing
                $this->sendResponse(['success' => false, 'message' => 'Product ID and type are required.']);
            }
        }
    }

    /**
     * Updates the quantity of a product in the shopping cart.
     *
     * This function verifies the request, retrieves the product ID, product type, and new quantity from the POST data,
     * and then calls the `updateProductQuantityInCart` method to update the cart. It sends a JSON response with the result.
     *
     * @return void Outputs a JSON response.
     */
    public function updateProductQuantity(): void
    {
        // Check if the current request is a POST request
        if ($this->isPostRequest()) {
            // Read the JSON data from the request body
            $jsonRequestBody = file_get_contents('php://input');
            $requestData = json_decode($jsonRequestBody, true);

            // Retrieve the product ID from POST data, default to null if not set
            $productId = $requestData['productId'] ?? null;
            $productType = $requestData['productType'] ?? null;
            $newQuantity = $requestData['newQuantity'] ?? 0;

            // Check if the product ID and type are provided and the new quantity is valid
            if ($productId !== null && $productType !== null && $newQuantity > 0) {
                try {
                    // Call the method to update the product quantity in the cart
                    $result = CartController::updateProductQuantityInCart($productId, $productType, $newQuantity);
                    // Send the response back with the result
                    $this->sendResponse(['success' => $result]);
                } catch (Exception $e) {
                    // Handle any exceptions by sending an error response
                    $this->sendResponse(['success' => false, 'message' => $e->getMessage()]);
                }
            } else {
                // Send an error response if product ID, type, or new quantity is missing or invalid
                $this->sendResponse(['success' => false, 'message' => 'Invalid product information or quantity.']);
            }
        }
    }

    public function updatestockQuantity(): void
    {
        // Check if the current request is a POST request
        if ($this->isPostRequest()) {
            // Read the JSON data from the request body
            $jsonRequestBody = file_get_contents('php://input');
            $requestData = json_decode($jsonRequestBody, true);

            // Retrieve the product ID from POST data, default to null if not set
            $productId = $requestData['stockId'] ?? null;
            $productType = $requestData['stockType'] ?? null;
            $newQuantity = $requestData['newQuantity'] ?? 0;

            // Check if the product ID and type are provided and the new quantity is valid
            if ($productId !== null && $productType !== null && $newQuantity > 0) {
                try {
                    // Call the method to update the product quantity in the cart
                    $result = AdminController::updateStockQuantity($productId, $productType, $newQuantity);
                    // Send the response back with the result
                    $this->sendResponse(['success' => $result]);
                } catch (Exception $e) {
                    // Handle any exceptions by sending an error response
                    $this->sendResponse(['success' => false, 'message' => $e->getMessage()]);
                }
            } else {
                // Send an error response if product ID, type, or new quantity is missing or invalid
                $this->sendResponse(['success' => false, 'message' => 'Invalid product information or quantity.']);
            }
        }
    }

    /**
     * Adds a custom pizza to the shopping cart.
     * Validates the request, processes the custom pizza creation, and adds it to the cart.
     * It checks for the number of ingredients to remove/add and validates the supplement IDs.
     *
     * @return void Outputs a JSON response with the result of the operation.
     */
    public function addCustomPizzaToCart(): void
    {
        // Check if the current request is a POST request
        if ($this->isPostRequest()) {

            $jsonRequestBody = file_get_contents('php://input');
            $requestData = json_decode($jsonRequestBody, true);

            $productId = $requestData['productId'] ?? 'null';
            $ingredientsToRemove = $requestData['ingredientsToRemove'] ?? [];
            $supplementsToAdd = $requestData['supplementsToAdd'] ?? [];
            $productQuantity = $requestData['productQuantity'] ?? 'null';

            if (count($ingredientsToRemove) > 3) {
                $this->sendResponse(['success' => false, 'message' => 'You cannot remove more than 3 ingredients.']);
            }

            if (count($supplementsToAdd) > 3) {
                $this->sendResponse(['success' => false, 'message' => 'You cannot add more than 3 supplements.']);
            }

            foreach ($supplementsToAdd as $supplement) {
                if (!($supplement == 7 || $supplement == 17 || $supplement == 2)) {
                    $this->sendResponse(['success' => false, 'message' => 'Invalid supplement.']);
                }
            }

            if (!isset($productQuantity) || $productQuantity < 1) {
                $this->sendResponse(['success' => false, 'message' => 'Invalid product quantity']);
            }

            if ($productId !== null) {
                try {
                    $params = [
                        'p_originalPizzaId' => $productId,
                        'p_addedIngredient1' => $supplementsToAdd[0] ?? null,
                        'p_addedIngredient2' => $supplementsToAdd[1] ?? null,
                        'p_addedIngredient3' => $supplementsToAdd[2] ?? null,
                        'p_removedIngredient1' => $ingredientsToRemove[0] ?? null,
                        'p_removedIngredient2' => $ingredientsToRemove[1] ?? null,
                        'p_removedIngredient3' => $ingredientsToRemove[2] ?? null
                    ];
                    $customPizzaId = -1;
                    $outParams = ['p_customPizzaId' => &$customPizzaId];

                    DB_Connection::callProcedure('CreateCustomPizza', $params, $outParams);
                    $result = CartController::addProductToCart($customPizzaId, 'pizzaCustom', $productQuantity);
                    $this->sendResponse(['success' => $result]);
                } catch (Exception $e) {
                    $this->sendResponse(['success' => false, 'message' => $e->getMessage()]);
                }
            } else {
                $this->sendResponse(['success' => false, 'message' => 'Invalid product id.']);
            }
        }
    }

    /**
     * Deletes an item from the database.
     * This method reads the product ID and type from the request and attempts to delete the item.
     *
     * @return void Outputs a JSON response with the result of the delete operation.
     */
    public function deleteItem(): void
    {
        if ($this->isPostRequest()) {

            $jsonRequestBody = file_get_contents('php://input');
            $requestData = json_decode($jsonRequestBody, true);

            $productId = $requestData['productId'] ?? 'null';
            $productType = $requestData['productType'] ?? 'null';

            if ($productId !== null && $productType !== null) {
                try {
                    $result = AdminController::deleteObject($productId, $productType);
                    $this->sendResponse(['success' => $result]);
                } catch (Exception $e) {
                    $this->sendResponse(['success' => false, 'message' => $e->getMessage()]);
                }
            } else {
                $this->sendResponse(['success' => false, 'message' => 'Invalid product id or type.']);
            }
        }
    }

    /**
     * Retrieves the top-selling products for the last 30 days.
     * This method queries the database and returns a list of top-selling products.
     *
     * @return void Outputs a JSON response with the list of top products.
     */
    public function getTopProducts(): void
    {
        if ($this->isPostRequest()) {
            $topProducts = DB_Helper::getTopProductsLast30Days();
            $this->sendResponse(['success' => true, 'topProducts' => $topProducts]);
        }
    }

    /**
     * Fetches sales data grouped by month.
     * This method queries the database for sales data and returns it categorized by month.
     *
     * @return void Outputs a JSON response with sales data by month.
     */
    public function getSalesByMonth(): void
    {
        if ($this->isPostRequest()) {
            $salesByMonth = DB_Helper::getSalesByMonth();
            $this->sendResponse(['success' => true, 'salesByMonth' => $salesByMonth]);
        }
    }

    /**
     * Retrieves statistical data about pizzas.
     * This method queries the database for pizza statistics and returns the data.
     *
     * @return void Outputs a JSON response with pizza statistics.
     */
    public function getPizzaStats(): void
    {
        if ($this->isPostRequest()) {
            $pizzaStats = DB_Helper::getPizzaStats();
            $this->sendResponse(['success' => true, 'pizzaStats' => $pizzaStats]);
        }
    }

    /**
     * Updates the spotlight status of a specified product.
     * Reads the product ID, type, and new spotlight status from the request and updates the database.
     *
     * @return void Outputs a JSON response with the result of the update operation.
     */
    public function updateSpotlight(): void
    {
        if ($this->isPostRequest()) {
            $jsonRequestBody = file_get_contents('php://input');
            $requestData = json_decode($jsonRequestBody, true);

            $productId = $requestData['productId'] ?? 'null';
            $productType = $requestData['productType'] ?? 'null';
            $isSpotlight = $requestData['isSpotlight'] ?? 'null';

            if ($productId !== null && $productType !== null && $isSpotlight !== null) {
                try {
                    $result = AdminController::updateSpotlight($productId, $productType, $isSpotlight);
                    $this->sendResponse(['success' => $result]);
                } catch (Exception $e) {
                    $this->sendResponse(['success' => false, 'message' => $e->getMessage()]);
                }
            } else {
                $this->sendResponse(['success' => false, 'message' => 'Invalid product id or type.']);
            }
        }
    }
}
