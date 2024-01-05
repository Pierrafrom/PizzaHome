<?php

namespace App\helpers;

use App\DB_Connection;
use Exception;
use PDOException;

/**
 * A helper class for database operations related to user authentication, email checks, and retrieving statistical data.
 */
class DB_Helper
{
    /**
     * Verifies if the user's credentials are valid.
     *
     * This method calls a stored procedure for user login and checks if the provided
     * email and password match the records in the database.
     *
     * @param string $email The user's email address.
     * @param string $password The user's password.
     *
     * @return int|null Returns the user's ID if the credentials are valid.
     * @throws Exception Throws an exception if no user is found with the given email, or if the password is invalid.
     */
    public static function verifyCredentials(string $email, string $password): int | null
    {
        // Call the stored procedure for user login
        $procedureName = 'UserLogin';
        $params = ['inputEmail' => $email];
        $outParams = ['userID' => null, 'hashedPassword' => null];
        DB_Connection::callProcedure($procedureName, $params, $outParams);

        // Check if a user has been found
        if ($outParams['userID'] == -1) {
            throw new Exception("No user found with the given email.");
        }

        // Verify the password
        // The password_verify function checks if the given password matches the hashed password
        // The salt is automatically retrieved from the hashed password.
        if (!password_verify($password, $outParams['hashedPassword'])) {
            throw new Exception("Invalid password.");
        }

        // Return the user's ID
        return $outParams['userID'];
    }

    /**
     * Checks if an email exists in the database.
     *
     * This function calls a stored procedure 'CheckEmailExists' in the database
     * to determine if the given email address exists in the database. It uses
     * a predefined database connection and handles input and output parameters
     * for the procedure call.
     *
     * @param string $email The email address to be checked.
     *
     * @return bool Returns true if the email exists, false otherwise.
     */
    public static function checkEmailExists(string $email): bool
    {
        // Name of the stored procedure
        $procedureName = 'CheckEmailExists';

        // Parameters to be passed to the stored procedure
        $params = ['inputEmail' => $email];

        // Output parameters expected from the stored procedure
        $outParams = ['emailExists' => false];

        // Calling the stored procedure with input and output parameters
        DB_Connection::callProcedure($procedureName, $params, $outParams);

        // Return the result from the output parameter
        return $outParams['emailExists'];
    }

    /**
     * Handles administrator login by verifying credentials.
     *
     * Calls a stored procedure for admin login and validates the provided email and password.
     *
     * @param string $email The admin's email address.
     * @param string $password The admin's password.
     *
     * @return int|null Returns the admin's ID if credentials are valid, null if not.
     * @throws Exception Throws an exception if no admin is found with the given email, or if the password is invalid.
     */
    public static function adminLogin(string $email, string $password): int|null
    {
        // Call the stored procedure for user login
        $procedureName = 'AdminLogin';
        $params = ['inputEmail' => $email];
        $outParams = ['userID' => null, 'hashedPassword' => null];
        DB_Connection::callProcedure($procedureName, $params, $outParams);

        // Check if a user has been found
        if ($outParams['userID'] == -1) {
            throw new Exception("No user found with the given email.");
        }

        // Verify the password
        // The password_verify function checks if the given password matches the hashed password
        // The salt is automatically retrieved from the hashed password.
        if (!password_verify($password, $outParams['hashedPassword'])) {
            throw new Exception("Invalid password.");
        }

        // Return the user's ID
        return $outParams['userID'];
    }


    /**
     * Retrieves top selling products for the last 30 days.
     *
     * Executes a query to fetch data from a view that contains information about top-selling products.
     *
     * @return array An array of products and their sales data.
     */
    public static function getTopProductsLast30Days(): array
    {
        // SQL query to get data from the view
        $sql = "SELECT * FROM VIEW_TOP_PRODUCTS_LAST_30_DAYS";

        try {
            return DB_Connection::query($sql);
        } catch (PDOException $e) {
            throw new PDOException("Erreur lors de la récupération des données : " . $e->getMessage());
        }
    }

    /**
     * Fetches sales data categorized by month.
     *
     * Executes a SQL query to retrieve data regarding revenue for the last six months.
     *
     * @return array An array containing monthly sales data.
     */
    public static function getSalesByMonth(): array
    {
        // SQL query to get data from the view
        $sql = "SELECT * FROM VIEW_REVENUE_LAST_6_MONTHS";

        try {
            return DB_Connection::query($sql);
        } catch (PDOException $e) {
            throw new PDOException("Erreur lors de la récupération des données : " . $e->getMessage());
        }
    }

    /**
     * Retrieves statistical data about pizza sales.
     *
     * Executes a SQL query to fetch statistical data from a view that contains information about pizza sales.
     *
     * @return array An array of pizza statistics.
     */
    public static function getPizzaStats(): array
    {
        // SQL query to get data from the view
        $sql = "SELECT * FROM VIEW_PIZZA_STATS";

        try {
            return DB_Connection::query($sql);
        } catch (PDOException $e) {
            throw new PDOException("Erreur lors de la récupération des données : " . $e->getMessage());
        }
    }
}
