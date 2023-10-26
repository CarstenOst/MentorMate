<?php

namespace Application\Views\User;

require ("../../../autoloader.php");
use Application\Views\Shared\Layout;
use Core\Entities\User;
use Infrastructure\Repositories\UserRepository;

class Profile
{
    public static function viewUserProfile(int $userId) {
        $user = UserRepository::read($userId);
        if (is_string($user)) {
            echo $user; // TODO call some common error msg display
            return;
        }
        $firstName = $user->getFirstName();
        $lastName = $user->getLastName();
        $userType = $user->getUserType();
        $email = $user->getEmail();
        $about = $user->getAbout();
        echo "
            <div class='user-profile'>
                <h5>$firstName $lastName</h5>
                <p><small>$userType</small></p>
                <p>Email: $email</p>
                <p>Bio: $about</p>
            </div>
        ";
    }
}

?>


<html>
    <?php
        Layout::displayTop();
        Profile::viewUserProfile(1);
    ?>
</html>
