<?php

namespace Database;
include_once 'DBConfig.php'; // This file is not included on GitHub for security reasons. Make one yourself
// with DB_HOST_NAME, DB_NAME and DB_PASSWORD constants
// Note: you might have to add another constant for the username, as our db has same name and username for simplicity

use PDOException;
use PDO;

class DBConnector
{
    public static function getConnection(): ?PDO
    {
        try {
            $conn = new PDO(
                "mysql:host=" . DB_HOST_NAME . ";dbname=" . DB_NAME,
                DB_NAME,
                DB_PASSWORD);

            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $conn->exec("set names utf8");
        } catch (PDOException $exception) {
            // TODO add logging
            // TODO do not echo exceptions to the user
            echo "Connection error: " . $exception->getMessage();
        }

        return $conn ?? null;
    }
}
