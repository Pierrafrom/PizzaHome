<?php

namespace App\controllers;

use App\DB_Connection;
use App\helpers\DB_Helper;
use App\helpers\SessionHelper;
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
     * RegistrationController constructor.
     *
     * Initializes the RegistrationController instance. This constructor sets up
     * the basic configurations required for rendering the registration and login pages.
     * It establishes the page title as "Registration" and specifies the CSS and JavaScript
     * files necessary for the styling and functionality of these pages.
     *
     * The constructor first calls the parent constructor of the Controller class for general
     * initialization tasks, such as setting the view path. Then, it customizes the setup for
     * the registration and login pages by specifying relevant CSS files (like 'banner.css' and
     * 'form.css') for page styling, and JavaScript files ('tabs.js' for tab functionality
     * and 'registration.js' for handling registration and login processes) to enhance the
     * user experience and interactivity on these pages.
     *
     * @param string $viewPath The base path for the view files specific to the RegistrationController.
     */
    public function __construct(string $viewPath)
    {
        parent::__construct($viewPath); // Call the parent constructor for basic setup
        self::$title = "Registration"; // Set the title specific to the registration and login pages
        self::$cssFiles = ["form.css"]; // Define CSS files for page styling
        self::$scriptFiles = ["tabs.js"]; // Include JavaScript files for tab functionality
        self::$moduleFiles = ["registration.js"]; // Include JavaScript modules for registration and login processes
    }

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

            // Connect the user to a session
            SessionHelper::sessionConnect($id);

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
    #[NoReturn] public function logout(): void
    {
        SessionHelper::destroySession();

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

        // Hash the password using the password_hash function. This function uses the bcrypt algorithm.
        // The PASSWORD_DEFAULT constant is used to ensure that the algorithm used is always the
        // most secure algorithm available on the server.
        // See https://www.php.net/manual/en/function.password-hash.php for more information.
        // The salt is generated automatically by the function.
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Call the stored procedure for registration
        try {
            if (DB_Helper::checkEmailExists($email)) {
                throw new Exception("Email already exists");
            }

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

            // Connect the user to a session
            SessionHelper::sessionConnect((int)$outParams['clientID']);

            // Redirect to the home page or the requested redirect page if valid
            URL::redirect($_POST['redirect'] ?? '/');

        } catch (PDOException $e) {
            // Handle database-related exceptions
            throw new Exception("Database error: " . $e->getMessage());
        }
    }
}