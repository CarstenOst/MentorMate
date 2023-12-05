<?php

namespace Infrastructure\Database;
// This file is not included on GitHub for security reasons. Make one yourself
// with DB_HOST_NAME, DB_NAME and DB_PASSWORD constants
// Note: you might have to add another constant for the username, as our db has same name and username for simplicity

use PDO;
use PDOException;

class DBConnector
{
    /**
     * This static method returns a PHP Data Object (PDO) connection to the database.
     * Note, remember to add DBConfig.php with connection constants.
     *
     * @return PDO|null Returns a PDO connection to the database, or null if an exception is thrown.
     * @throws PDOException Throws a PDOException if the connection fails.
     *
     */
    public static function getConnection(): ?PDO
    {
        try {
            $conn = new PDO(
                "mysql:host=" . DBConfig::DB_HOST_NAME . ";dbname=" . DBConfig::DB_NAME,
                DBConfig::DB_NAME,
                DBConfig::DB_PASSWORD);

            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $conn->exec("set names utf8");
        } catch (PDOException $exception) {
            // TODO add logging, and handle this somehow
            echo "Connection error: please set up the database correctly :)";
        }

        return $conn ?? null;
    }
}
