<?php

namespace App\controllers;

use App\helpers\DB_Helper;
use Exception;

/**
 * ApiController class to handle API requests related to user authentication.
 *
 * This class includes methods for verifying user credentials and responding
 * accordingly. It's typically used for handling AJAX requests from the frontend.
 */
class ApiController
{
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
        // Decode JSON data from the request body
        $postData = json_decode(file_get_contents('php://input'), true);

        // Retrieve email and password from the decoded data
        $email = $postData['email'] ?? '';
        $password = $postData['password'] ?? '';

        // Check the credentials
        // In this example, assume that checkCredentials returns true if credentials are correct
        try {
            DB_Helper::verifyCredentials($email, $password);
            $isCorrect = true;
        } catch (Exception) {
            $isCorrect = false;
        }

        // Respond with JSON indicating success or failure
        if ($isCorrect) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Incorrect credentials']);
        }
    }
}
