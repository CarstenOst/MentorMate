<?php

namespace Infrastructure\Repositories;

use Infrastructure\Database\DBConnector;
use Core\Interfaces\IUserRepository;
use Core\Entities\User;
use PDOStatement;
use DateTime;
use PDO;

class UserRepository implements IUserRepository
{

    public static function create($user): bool
    {
        $query = "INSERT INTO User (
                  firstName, 
                  lastName, 
                  email, 
                  password, 
                  userType, 
                  about, 
                  createdAt, 
                  updatedAt) 
                VALUES (:firstName, 
                        :lastName, 
                        :email, 
                        :password, 
                        :userType, 
                        :about, 
                        :createdAt, 
                        :updatedAt)
                        ";
        $sql = self::getSql($query, $user);

        // Execute the statement and return true if successful, false otherwise
        return $sql->execute();
    }



    /**
     * @param $id
     * @return User|String
     * @throws Exception
     */
    public static function read($id): User|String {

        $query = "SELECT * FROM User WHERE userId = ? LIMIT 0,1";
        $connection = DBConnector::getConnection();
        $sql = $connection->prepare($query);
        $sql->bindParam(1, $id, PDO::PARAM_INT); // To avoid SQL injection
        $sql->execute();

        $row = $sql->fetch(PDO::FETCH_ASSOC);

        $user = new User();
        if ($row) {
            // map results to object properties
            $user->setUserId($id); // Faster lookup, as we already have the id
            $user->setAbout($row['about']);
            $user->setEmail($row['email']);
            $user->setUserType($row['userType']);
            $user->setPassword($row['password']);
            $user->setLastName($row['lastName']);
            $user->setFirstName($row['firstName']);
            $user->setCreatedAt(new DateTime($row['createdAt']) ?? null); // Could cause exception
            $user->setUpdatedAt(new DateTime($row['updatedAt']) ?? null);
            return $user;
        }
        return 'User was not found';
    }

    public static function update($user): bool
    {
        $query = "UPDATE User SET 
                firstName=:firstName, 
                lastName=:lastName, 
                email=:email, 
                password=:password, 
                userType=:userType, 
                about=:about, 
                createdAt=:createdAt, 
                updatedAt=:updatedAt 
            WHERE userId=:userId";
        $sql = self::getSql($query, $user);
        $sql->bindValue(':userId', $user->getUserId(), PDO::PARAM_INT);

        // Execute the statement and return true if successful, false otherwise
        return $sql->execute();
    }


    public static function delete($id): bool
    {
        $query = "DELETE FROM User WHERE userId = ?";
        $connection = DBConnector::getConnection();
        $sql = $connection->prepare($query);

        // Bind the ID parameter
        $sql->bindParam(1, $id, PDO::PARAM_INT);

        // Execute the statement and return true if successful, false otherwise
        return $sql->execute();
    }


    /**
     * @param string $query
     * @param $user
     * @return PDOStatement
     */
    private static function getSql(string $query, $user): PDOStatement
    {
        $connection = DBConnector::getConnection();
        $sql = $connection->prepare($query);

        // Bind parameters using named parameters and bindValue
        $sql->bindValue(':firstName', $user->getFirstName());
        $sql->bindValue(':lastName', $user->getLastName());
        $sql->bindValue(':email', $user->getEmail());
        $sql->bindValue(':password', $user->getPassword());
        $sql->bindValue(':userType', $user->getUserType());
        $sql->bindValue(':about', $user->getAbout());
        $sql->bindValue(':createdAt', $user->getCreatedAt()->format('Y-m-d H:i:s'));
        $sql->bindValue(':updatedAt', $user->getUpdatedAt()->format('Y-m-d H:i:s'));

        return $sql;
    }
}