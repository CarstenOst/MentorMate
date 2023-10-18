<?php
namespace Application\Controllers;
require '../../autoloader.php';

use Infrastructure\Repositories\UserRepository;

class UserController
{

    public static function showUser($id): void
    {
        $user = UserRepository::read($id);
        if (is_string($user)) {
            echo $user;
            return;
        }
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
