<?php

namespace App;

use PDO;
use PDOException;

// Include the database configuration file.
include_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'db_config.php';

/**
 * Class DB_Connection
 *
 * Handles the creation and retrieval of the PDO connection instance.
 */
class DB_Connection
{
    /**
     * @var PDO|null Holds the PDO instance.
     */
    private static ?PDO $pdo = null;

    /**
     * Constructs the Data Source Name (DSN) string required for the PDO connection.
     *
     * @return string The DSN string.
     */
    private static function getDSN(): string
    {
        // Construct and return the DSN string using defined constants.
        return "mysql:host=" . HOSTNAME . ";dbname=" . DATABASE . ";charset=utf8";
    }

    /**
     * Retrieves the PDO instance, initiating a connection if not already connected.
     *
     * @return PDO The PDO instance.
     * @throws PDOException If unable to connect to the database.
     */
    public static function getPDO(): PDO
    {
        // Check if PDO is null and establish connection if so.
        if (self::$pdo === null) {
            self::connect();
        }

        // Return the PDO instance.
        return self::$pdo;
    }

    /**
     * Initiates a PDO connection using configurations and sets attributes.
     *
     * @return void
     * @throws PDOException If unable to connect to the database.
     */
    private static function connect(): void
    {
        try {
            // Attempt to instantiate PDO, configure options.
            self::$pdo = new PDO(
                self::getDSN(),
                LOGIN,
                PASSWORD,
                [
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8",
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
                ]
            );
        } catch (PDOException $e) {
            // Handle connection failure.
            throw new PDOException("Connection failed: " . $e->getMessage());
        }
    }

    /**
     * Executes a SQL query and returns the result as an array or an object of a specified class.
     *
     * @param string $sql The SQL query to be executed.
     * @param array $params An associative array of parameters to bind to the SQL query.
     * @param string|null $class The fully-qualified name of the class to map the result to, or null to fetch as an associative array.
     *
     * @return array|object The result of the query, as an array or an object of the specified class.
     *
     * @throws PDOException If the query fails to execute.
     */
    public static function query(string $sql, array $params = [], ?string $class = null): object|array
    {
        // Ensure PDO is connected.
        $pdo = self::getPDO();

        try {
            // Prepare the SQL statement.
            $stmt = $pdo->prepare($sql);

            // Bind parameters.
            foreach ($params as $key => $value) {
                $stmt->bindValue(':' . $key, $value);
            }

            // Execute the SQL statement.
            $stmt->execute();

            // Fetch the results into the specified class or as an associative array.
            if ($class === null) {
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            } else {
                return $stmt->fetchAll(PDO::FETCH_CLASS, $class);
            }
        } catch (PDOException $e) {
            // Handle query failure.
            throw new PDOException("Query failed: " . $e->getMessage());
        }
    }
}
