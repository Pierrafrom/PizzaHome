<?php

namespace App\helpers;

use App\DB_Connection;
use Exception;

class DB_Helper
{
    /**
     * Verifies if the user's credentials are valid.
     *
     * This method calls a stored procedure for user login and checks if the provided
     * email and password match the records in the database.
     *
     * @param string $email    The user's email address.
     * @param string $password The user's password.
     *
     * @return int Returns the user's ID if the credentials are valid.
     * @throws Exception Throws an exception if no user is found with the given email, or if the password is invalid.
     */
    public static function verifyCredentials(string $email, string $password): int
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
        if (!password_verify($password, $outParams['hashedPassword'])) {
            throw new Exception("Invalid password.");
        }

        // Return the user's ID
        return $outParams['userID'];
    }
}
