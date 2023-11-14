<?php

namespace App\helpers;

use Exception;
use JetBrains\PhpStorm\NoReturn;

/**
 * Provides utility methods for URL handling within an application.
 *
 * The URL class in the App\Helpers namespace offers functionalities related to URL processing.
 * It currently includes a method for redirecting to a specified URL. This class can be expanded
 * to include more URL-related utilities as needed.
 */
class URL
{
    /**
     * Redirects to a specified URL.
     *
     * This method validates the provided URL and redirects the browser to it. If the URL is not valid,
     * it defaults to redirecting to the home page ('/'). This method terminates script execution
     * after sending the redirect header.
     *
     * @param string $url The URL to redirect to.
     *
     * @return void
     * @throws Exception If the header operation fails.
     */
    #[NoReturn]
    public static function redirect(string $url): void
    {
        // Validate the URL, defaulting to the home page ('/') if invalid
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            $url = '/';
        }

        // Send a redirect header to the browser
        header('Location: ' . $url);

        // Terminate the script to prevent further execution
        exit;
    }
}