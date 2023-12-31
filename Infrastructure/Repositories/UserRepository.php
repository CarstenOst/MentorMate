<?php

namespace Infrastructure\Repositories;

use Infrastructure\Database\DBConnector;
use Core\Interfaces\IUserRepository;
use Core\Entities\User;
use PDOException;
use PDOStatement;
use Exception;
use DateTime;
use PDO;

class UserRepository implements IUserRepository
{

    /**
     * @param User $user
     * @return int
     * @throws Exception
     */
    public static function create(User $user): int
    {
        $query = "INSERT INTO User (
                  firstName, 
                  lastName, 
                  email, 
                  password, 
                  userType, 
                  about) 
                VALUES (:firstName, 
                        :lastName, 
                        :email, 
                        :password, 
                        :userType, 
                        :about);
                        ";


        $connection = DBConnector::getConnection();
        $sql = $connection->prepare($query);

        // Bind parameters using named parameters and bindValue
        $sql->bindValue(':firstName', $user->getFirstName());
        $sql->bindValue(':lastName', $user->getLastName());
        $sql->bindValue(':password', $user->getPassword());
        $sql->bindValue(':userType', $user->getUserType());
        $sql->bindValue(':about', $user->getAbout());
        $sql->bindValue(':email', $user->getEmail());

        try {
            // Execute the statement
            $sql->execute();
            return $connection->lastInsertId();

        } catch (PDOException $e) {
            // Check if the error is due to a duplicate key on 'email'
            if ($e->getCode() == ErrorCode::DUPLICATE_EMAIL ||
                str_contains($e->getMessage(), 'Duplicate entry'))
            {
                // Handle duplicate email error
                return -ErrorCode::DUPLICATE_EMAIL;
            } else {
                // Handle other errors or rethrow the exception
                throw $e;
            }
        }
    }

    /**
     * @param int $id
     * @return User|string
     * @throws Exception
     */
    public static function read($id): User|string
    {
        $query = "SELECT * FROM User WHERE userId = :id LIMIT 0,1";
        $connection = DBConnector::getConnection();

        try {
            $sql = $connection->prepare($query);
            $sql->bindParam(':id', $id, PDO::PARAM_INT);
            $sql->execute();

            $row = $sql->fetch(PDO::FETCH_ASSOC);

            if ($row) {
                return self::createUserFromRow($row);
            } else {
                return 'User was not found';
            }
        } catch (PDOException $e) {
            // We should log the error or handle it appropriately, not like this of course
            return 'Database error: something went wrong when trying to read user';
        } finally {
            $connection = null;  // Close the connection
        }
    }

    public static function getUsersByType(string $type): array
    {
        $query = "SELECT * FROM User WHERE userType=:userType";
        $connection = DBConnector::getConnection();
        $sql = $connection->prepare($query);
        $sql->execute(['userType' => $type]);

        $result = $sql->fetchAll(PDO::FETCH_ASSOC);
        $users = [];
        if ($result) {
            foreach ($result as $row) {
                $users[] = self::createUserFromRow($row);
            }
        }

        return $users;
    }


    /**
     * @param string $email
     * @return User|string
     * @throws Exception
     */
    public static function getUserByEmail(string $email): User|string
    {
        $query = "SELECT * FROM User WHERE email=:email";
        $connection = DBConnector::getConnection();
        $sql = $connection->prepare($query);
        $sql->execute(['email' => $email]);

        $row = $sql->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            return self::createUserFromRow($row);
        }

        return 'Not valid';
    }


    /**
     * TODO test this
     * @param $user
     * @return bool
     */
    public static function update($user): bool
    {
        $query = "UPDATE User SET 
                firstName=:firstName, 
                lastName=:lastName, 
                password=:password, 
                userType=:userType, 
                email=:email, 
                about=:about  
            WHERE userId=:userId";
        $sql = self::getSql($query, $user);
        $sql->bindValue(':userId', $user->getUserId(), PDO::PARAM_INT);

        return $sql->execute();
    }

    /**
     * Update the about section of a user
     *
     * @param int $userId
     * @param string $about
     * @return bool true if successful, false otherwise
     */
    public static function updateAbout(int $userId, string $about): bool
    {
        $query = "UPDATE User SET about=:about WHERE userId=:userId";
        $connection = DBConnector::getConnection();
        $sql = $connection->prepare($query);

        $sql->bindValue(':about', $about);
        $sql->bindValue(':userId', $userId, PDO::PARAM_INT);

        return $sql->execute();
    }


    /**
     * function to delete a user, obviously won't work if the user has bookings, or messages
     * @param $id
     * @return bool
     */
    public static function delete($id): bool
    {
        $query = "DELETE FROM User WHERE userId = :id";
        $connection = DBConnector::getConnection();
        $sql = $connection->prepare($query);

        // Bind the ID parameter
        $sql->bindParam(':id', $id, PDO::PARAM_INT);

        // Execute the statement and return true if successful, false otherwise
        return $sql->execute();
    }


    /**
     * Just to reuse some sql code, but it was later deprecated (sorta)
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
        $sql->bindValue(':password', $user->getPassword());
        $sql->bindValue(':userType', $user->getUserType());
        $sql->bindValue(':about', $user->getAbout());
        $sql->bindValue(':email', $user->getEmail());

        return $sql;
    }


    /**
     * Populate a User object from a database row.
     *
     * @param array $row The database row.
     * @return User The populated User object.
     * @throws Exception
     */
    private static function createUserFromRow(array $row): User
    {
        $user = new User();
        $user->setAbout($row['about']);
        $user->setEmail($row['email']);
        $user->setUserId($row['userId']);
        $user->setUserType($row['userType']);
        $user->setPassword($row['password']);
        $user->setLastName($row['lastName']);
        $user->setFirstName($row['firstName']);
        $user->setCreatedAt(new DateTime($row['createdAt']) ?? null);
        $user->setUpdatedAt(new DateTime($row['updatedAt']) ?? null);

        return $user;
    }
}
