<?php
namespace Application\Controllers;
require '../../autoloader.php';

use Exception;
use Infrastructure\Repositories\UserRepository;

class UserController
{


    /**
     * Controller to show the user.
     *
     * @param int $id Insert the userid to search for as int.
     * @return void TODO change to return ok status or something.
     * @throws Exception if datetime is not correctly set in the database.
     */
    public static function showUser(int $id): void
    {
        $user = UserRepository::read($id);
        if (is_string($user)) {
            echo $user; // TODO call some common error msg display
            return;
        }
        include_once '../Views/User/Login.php';
        echo "User Type: " . $user->getUserType();
        echo "<br>User Name: " . $user->getFirstName();
        echo "<br>User Last Name: " . $user->getLastName();
        echo "<br>User Mail: " . $user->getEmail();
        echo "<br>User About: " . $user->getAbout();
        echo "<br>User Created At: " . $user->getCreatedAt()->format('Y-m-d H:i:s');
        echo "<br>User Updated At: " . $user->getUpdatedAt()->format('Y-m-d H:i:s');
        echo "<br>User Password: " . $user->getPassword();
    }

}

UserController::showUser(1);
