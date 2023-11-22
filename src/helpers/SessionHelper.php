<?php

namespace App\helpers;

/**
 * SessionHelper class to manage user sessions with enhanced security.
 *
 * This class contains static methods to start, manage, and configure user sessions in a secure manner.
 * It includes methods to create a session with secure cookie settings, to log a user in, and to initialize session variables.
 */
class SessionHelper
{
    /**
     * Initializes a user session with enhanced security settings.
     *
     * This method starts a new session if one hasn't already been started. It sets the session cookie parameters
     * with specific attributes to enhance security, including the 'SameSite' attribute to comply with modern browser
     * requirements. After setting these parameters, it initiates a session.
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
     */
    private static function createSession(): void
    {
        // Check if a session hasn't already been started
        if (session_status() === PHP_SESSION_NONE) {
            // Set session cookie parameters
            session_set_cookie_params([
                'lifetime' => 0, // set to 0 for a session cookie
                'path' => '/',
                'domain' => '', // specify your domain if necessary
                'secure' => false, // set to true if your site only operates on HTTPS
                'httponly' => true, // set to true to prevent access to the cookie via JavaScript
                'samesite' => 'Lax' // Values can be 'None' (recommended with https), 'Lax' (recommended with http) or 'Strict'
            ]);

            // Start the session
            session_start();
        }
    }

    /**
     * Logs in the user and sets session variables.
     *
     * This method calls createSession to ensure a secure session is started, then sets session variables to
     * indicate that the user is logged in. It stores the user's ID and email as session data.
     *
     * @param int $userId The user's ID.
     * @return void This function does not return a value.
     */
    public static function sessionConnect(int $userId): void
    {
        self::createSession();

        if (!isset($_SESSION['cart'])) {
            session_regenerate_id(true);
            $_SESSION['cart'] = array();
        } else {
            $cart = $_SESSION['cart'];
            session_regenerate_id(true);
            $_SESSION['cart'] = $cart;
        }

        // Set session variables to indicate the user is logged in
        $_SESSION['logged_in'] = true;
        $_SESSION['user_id'] = $userId;
    }

    /**
     * Initializes key session variables with null values.
     *
     * This method ensures that certain session variables ('logged_in', 'user_id', 'user_email', 'cart') are
     * initialized in the session. If they don't exist, they are set to null. It's useful to call this method
     * at the start of a session to initialize these variables.
     *
     * @return void This function does not return a value.
     */
    public static function initSession(): void
    {
        self::createSession();

        $keys = ['logged_in', 'user_id', 'user_email', 'cart'];

        foreach ($keys as $key) {
            if (!isset($_SESSION[$key])) {
                $_SESSION[$key] = null;
            }
        }
    }

    /**
     * Destroys the current session and clears all session data.
     *
     * This method effectively logs out a user by clearing all stored session data and destroying the session.
     * It also deletes the session cookie from the user's browser to ensure a complete disconnection.
     * This is particularly useful for implementing user logout functionality.
     *
     * @return void This function does not return a value.
     */
    public static function destroySession(): void
    {
        self::createSession();

        // Check if a session is currently active
        if (session_status() === PHP_SESSION_ACTIVE) {
            // Clear all session data
            $_SESSION = array();

            // Delete the session cookie
            if (ini_get("session.use_cookies")) {
                $params = session_get_cookie_params();
                setcookie(
                    session_name(),
                    '',
                    time() - 42000, // set the cookie to expire in the past
                    $params["path"],
                    $params["domain"],
                    $params["secure"],
                    $params["httponly"]
                );
            }

            // Destroy the session
            session_destroy();
        }
    }

}
