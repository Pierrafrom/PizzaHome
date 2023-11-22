<?php

namespace App\controllers;

use App\helpers\DB_Helper;
use Exception;
use JetBrains\PhpStorm\NoReturn;

/**
 * ApiController class to handle API requests related to user authentication.
 *
 * This class includes methods for verifying user credentials and responding
 * accordingly. It's typically used for handling AJAX requests from the frontend.
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

            try {
                DB_Helper::verifyCredentials($email, $password);
                $isCorrect = true;
            } catch (Exception) {
                $isCorrect = false;
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

}
