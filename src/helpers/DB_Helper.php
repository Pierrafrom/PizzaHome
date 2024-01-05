<?php

namespace App\helpers;

use App\DB_Connection;
use Exception;
use PDOException;

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
     * Récupère les données des produits les plus vendus des 30 derniers jours.
     *
     * @return array Retourne un tableau associatif des données.
     * @throws PDOException Si une erreur survient lors de l'exécution de la requête.
     */
    public static function getTopProductsLast30Days(): array
    {
        // SQL query to get data from the view
        $sql = "SELECT * FROM VIEW_TOP_PRODUCTS_LAST_30_DAYS";

        try {
            // Utilisation de la méthode query de la classe DB_Connection pour exécuter la requête
            return DB_Connection::query($sql);
        } catch (PDOException $e) {
            // Gestion des exceptions et renvoi d'un message d'erreur
            throw new PDOException("Erreur lors de la récupération des données : " . $e->getMessage());
        }
    }

    public static function getSalesByMonth(): array
    {
        // SQL query to get data from the view
        $sql = "SELECT * FROM VIEW_REVENUE_LAST_6_MONTHS";

        try {
            // Utilisation de la méthode query de la classe DB_Connection pour exécuter la requête
            return DB_Connection::query($sql);
        } catch (PDOException $e) {
            // Gestion des exceptions et renvoi d'un message d'erreur
            throw new PDOException("Erreur lors de la récupération des données : " . $e->getMessage());
        }
    }

    public static function getPizzaStats(): array
    {
        // SQL query to get data from the view
        $sql = "SELECT * FROM VIEW_PIZZA_STATS";

        try {
            // Utilisation de la méthode query de la classe DB_Connection pour exécuter la requête
            return DB_Connection::query($sql);
        } catch (PDOException $e) {
            // Gestion des exceptions et renvoi d'un message d'erreur
            throw new PDOException("Erreur lors de la récupération des données : " . $e->getMessage());
        }
    }
}
