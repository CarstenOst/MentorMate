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

            SELECT LAST_INSERT_ID() as id;
                        ";

        $sql = self::getSql($query, $user);

        try {
            // Execute the statement
            $sql->execute();
            return $sql->fetch(PDO::FETCH_ASSOC) ?? -1 ;

        } catch (PDOException $e) {
            // Check if the error is due to a duplicate key on 'email'
            if ($e->getCode() == 23000 &&
                str_contains($e->getMessage(), 'Duplicate entry') &&
                str_contains($e->getMessage(), 'for key \'email\''))
            {
                // Handle duplicate email error
                echo "Error: The provided email already exists in the database!";
                return ErrorCode::DUPLICATE_EMAIL;
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
            // You can log the error or handle it appropriately
            return 'Database error: ' . $e->getMessage(); // TODO just don't echo this later on
        } finally {
            $connection = null;  // Close the connection
        }
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

    public static function getUserPassword(int $userId): string {
        $query = "SELECT password FROM User WHERE userId=:userId";
        $connection = DBConnector::getConnection();

        $sql = $connection->prepare($query);
        $sql->bindParam(':userId', $userId, PDO::PARAM_INT);
        $sql->execute();

        $row = $sql->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            return $row['password'];
        }

        return 'User does not exist';
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
     * Get all users based on their role
     *
     * @param string $role Either Student or Tutor
     * @return array of User
     * @throws Exception If it fails
     */
    public static function getAllUsersByRole(string $role): array
    {
        $query = "SELECT * FROM User WHERE userType = :role";
        $connection = DBConnector::getConnection();
        $sql = $connection->prepare($query);

        $sql->execute(['role' => $role]);

        $users = [];
        while ($row = $sql->fetch(PDO::FETCH_ASSOC)) {
            $user = new User();
            $user->setAbout($row['about'] ?? '');
            $user->setEmail($row['email']);
            $user->setUserId($row['userId']);
            $user->setUserType($row['userType']);
            $user->setLastName($row['lastName']);
            $user->setFirstName($row['firstName']);
            $user->setCreatedAt(new DateTime($row['createdAt']) ?? null);
            $user->setUpdatedAt(new DateTime($row['updatedAt']) ?? null);

            $users[] = $user;
        }
        return $users;
    }


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
