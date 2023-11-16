<?php

namespace App\controllers;

use App\DB_Connection;
use App\helpers\DB_Helper;
use App\helpers\URL;
use Exception;
use JetBrains\PhpStorm\NoReturn;
use PDOException;

/**
 * Manages user registration, login, and logout processes.
 *
 * The RegistrationController class extends the base Controller class and is responsible
 * for handling user authentication and registration processes. It includes methods for logging in,
 * signing in (registering), logging out, and initializing user sessions. The class utilizes a
 * database connection for user authentication and session management.
 */
class RegistrationController extends Controller
{

    /**
     * Handles the user login process.
     *
     * This method is responsible for processing the login request. It checks the request
     * method, validates input data, and performs user authentication against the database.
     * If authentication is successful, it initializes a user session and redirects to a specified page.
     *
     * @throws Exception If the request method is not POST, or if email/password are not provided,
     *                   or if authentication fails, or if a database error occurs.
     */
    public function login(): void
    {
        // Ensure that the request is a POST request
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            throw new Exception("Invalid request method. Only POST is allowed.");
        }

        // Retrieve and validate email and password from POST data
        $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
        $password = $_POST['password'] ?? null;

        // Check if email and password are provided
        if (is_null($email) || is_null($password)) {
            throw new Exception("Email and password must be provided.");
        }

        try {
            $id = DB_Helper::verifyCredentials($email, $password);

            // update last login date
            $procedureName = 'UpdateLastLogin';
            $params = ['inputUserID' => $id];
            DB_Connection::callProcedure($procedureName, $params);

            // Initialize the session
            $this->createSession($id, $email);

            // Redirect to the home page or the requested redirect page if it's valid
            URL::redirect($_POST['redirect'] ?? '/');

        } catch (PDOException $e) {
            // Handle database-related exceptions
            throw new Exception("Database error: " . $e->getMessage());
        }
    }

    /**
     * Terminates the current user session and redirects to a specified page.
     *
     * This static method performs several actions to ensure that the user's session is
     * properly terminated. It can be used in any context where a user needs to be logged out.
     *
     * @return void
     * @throws Exception
     */
    #[NoReturn] public static function logout(): void
    {
        // Start the session if it has not already been started.
        // This is necessary because you cannot manipulate session variables if a session hasn't started.
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        // Destroy all session variables.
        // This clears any data stored in the session, effectively logging out the user.
        $_SESSION = array();

        // Check and destroy the session cookie if necessary.
        // This is important for security, ensuring that the session cannot be hijacked.
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000, // Set the cookie's expiration time in the past to delete it.
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }

        // Finally, destroy the session itself.
        session_destroy();

        // Redirect to the homepage or a requested redirect page if it's valid.
        $redirectUrl = filter_input(INPUT_GET, 'redirect', FILTER_SANITIZE_URL) ?: '/';
        URL::redirect($redirectUrl);
    }

    /**
     * Handles the user sign-in process.
     *
     * This method is responsible for processing the sign-in request. It checks the request
     * method, sanitizes and validates input data, and performs user registration against the database.
     * If the registration is successful, it initializes a user session and redirects to a specified page.
     *
     * @throws Exception If the request method is not POST, or if validation fails,
     *                   or if an error occurs during registration, or if a database error occurs.
     */
    public function signin(): void
    {
        // Ensure that the request is a POST request
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            throw new Exception("Invalid request method. Only POST is allowed.");
        }

        // Sanitize and validate inputs
        $lastName = filter_input(INPUT_POST, 'lastname', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $firstName = filter_input(INPUT_POST, 'firstname', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
        $password = $_POST['password'] ?? null;
        $confirmPassword = $_POST['confirm_password'] ?? null;

        // Validate country code with regex
        $countryCode = $_POST['country_code'] ?? '';
        if (!preg_match('/^\+\d{1,3}$/', $countryCode)) {
            throw new Exception("Invalid country code.");
        }

        // Validate phone number with regex
        $phone = $_POST['phone'] ?? '';
        if (!preg_match('/^\d{9,10}$/', $phone)) {
            throw new Exception("Invalid phone number.");
        }

        // Validate all data is provided
        if (!$lastName || !$firstName || !$countryCode || !$phone || !$email || !$password || !$confirmPassword) {
            throw new Exception("All fields must be filled out.");
        }

        // Check if passwords match
        if ($password !== $confirmPassword) {
            throw new Exception("Passwords do not match.");
        }

        // put last name in uppercase and first name in lowercase
        $lastName = strtoupper($lastName);
        $firstName = strtolower($firstName);

        // Assemble phone number and hash the password
        $fullPhone = $countryCode . $phone;
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Call the stored procedure for registration
        try {
            $procedureName = 'RegisterOrUpdateClient';
            $outParams = ['clientID' => null];
            $params = [
                'inputFirstName' => $firstName,
                'inputLastName' => $lastName,
                'inputEmail' => $email,
                'inputPhone' => $fullPhone,
                'inputPassword' => $hashedPassword
            ];
            DB_Connection::callProcedure($procedureName, $params, $outParams);

            // Check for successful registration
            if ($outParams['clientID'] == -1) {
                throw new Exception("An error occurred during registration.");
            }

            // Initialize the session
            $this->createSession((int)$outParams['clientID'], $email);

            // Redirect to the home page or the requested redirect page if valid
            URL::redirect($_POST['redirect'] ?? '/');

        } catch (PDOException $e) {
            // Handle database-related exceptions
            throw new Exception("Database error: " . $e->getMessage());
        }
    }

    /**
     * Initializes a user session with enhanced security settings.
     *
     * This method starts a new session if one hasn't already been started. It sets the session cookie parameters
     * with specific attributes to enhance security, including the 'SameSite' attribute to comply with modern browser
     * requirements. After setting these parameters, it initiates a session and sets session variables to
     * indicate that the user is logged in.
     *
     * @param int $userId The user's ID to be stored in the session.
     * @param string $email The user's email to be stored in the session.
     *
     * Session Cookie Parameters:
     * - 'lifetime': The duration of the cookie in seconds. Set to 0 for a session cookie.
     * - 'path': The path on the domain where the cookie is available.
     * - 'domain': The domain of the cookie. If empty, it defaults to the host name of the server which sets the cookie.
     * - 'secure': Indicates whether the cookie should be transmitted over a secure HTTPS connection. Set to true for
     * enhanced security.
     * - 'httponly': Flags the cookie to be accessible only through the HTTP protocol, not by client-side scripts.
     * - 'samesite': Controls when cookies are sent with requests. Values can be 'None', 'Lax', or 'Strict'. 'None'
     * allows sending cookies with cross-origin requests.
     *
     * @return void This function does not return a value.
     *
     * Note: Ensure your site is served over HTTPS when setting the 'SameSite' attribute to 'None'. This update is
     * necessary to comply with modern browser standards for cookie security and privacy.
     */
    private function createSession(int $userId, string $email): void
    {
        // Check if a session hasn't already been started
        if (session_status() === PHP_SESSION_NONE) {
            // Set session cookie parameters
            session_set_cookie_params([
                'lifetime' => 0, // or specify the lifetime of the cookie in seconds
                'path' => '/',
                'domain' => '', // specify your domain if necessary
                'secure' => false, // set to true if your site only operates on HTTPS
                'httponly' => true, // set to true to prevent access to the cookie via JavaScript
                'samesite' => 'Lax' // Values can be 'None' (recommended with https), 'Lax' (recommended with http) or 'Strict'
            ]);

            // Start the session
            session_start();
        }

        // Set session variables to indicate the user is logged in
        $_SESSION['logged_in'] = true;
        $_SESSION['user_id'] = $userId;
        $_SESSION['user_email'] = $email;
    }
}