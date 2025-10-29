<?php

namespace App\Repository;

use App\Utils\Config;
use PDO;

abstract class AbstractRepository
{
    private static ?PDO $connection = null;

    protected static function getConnection(): PDO {
        if (self::$connection === null) {
            $connection = Config::get('DB_CONNECTION');
            $username = Config::get('DB_USERNAME');
            $password = Config::get('DB_PASSWORD');
            $host = Config::get('DB_HOST');
            $port = Config::get('DB_PORT');
            $database = Config::get('DB_DATABASE');
            $charset = Config::get('DB_CHARSET');

            self::$connection = new PDO(
                "$connection:host=$host;port=$port;dbname=$database;charset=$charset",
                $username,
                $password
            );
            self::$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        return self::$connection;
    }
}