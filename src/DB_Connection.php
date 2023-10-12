<?php

namespace App;

use PDO;
use PDOException;

include_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'db_config.php';

class DB_Connection
{
    private static PDO $pdo;

    public static function getPDO(): PDO
    {
        return self::$pdo;
    }

    public static function connect(): void
    {
        try{
            self::$pdo = new PDO("mysql:host=".HOSTNAME.";dbname=".DATABASE, LOGIN, PASSWORD,
                array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
            self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }catch(PDOException $e){
            echo "Connection failed : ".$e->getMessage();
            die();
        }
    }

}