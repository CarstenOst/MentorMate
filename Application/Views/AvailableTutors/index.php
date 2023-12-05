<?php

require("../../../autoloader.php");

use Application\Constants\SessionConst;
use Application\Validators\Auth;
use Application\Views\Shared\Layout;
use Infrastructure\Repositories\UserRepository;


// Starts session, and checks if user is logged in. If not, redirects to login page
if (!Auth::checkAuth()) {
    header('Location: ./Login.php');
    exit();
}

// Check if the logout action is requested
if (isset($_GET['logout']) && $_GET['logout'] == 1) {
    // Call the logOut function from your class
    Auth::logOut();

    // Redirect to login page after logout
    header('Location: ../User/Login.php');
    exit();
}

?>


<html lang="en">
<head>
    <link rel="stylesheet" href="../../Assets/style.css">
    <script src="https://kit.fontawesome.com/5928831ae4.js" crossorigin="anonymous"></script>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script>
        // Waits for page to load, then makes functions available globally
        document.addEventListener("DOMContentLoaded", function () {
            window.viewUser = function viewUser(userId) {
                // Use AJAX to call a PHP controller action
                $.ajax({
                    type: "POST",
                    url: "../../Controllers/BookingController.php",
                    data: {
                        action: "viewUser",
                        userId: userId,
                    },
                    success: function (data) {
                        // Redirects so GET can post new date
                        const response = JSON.parse(data);
                        window.location.href = response.redirect;
                    }
                });
            }
        });
    </script>
</head>

<body>
<?php
$isTutor = $_SESSION[SessionConst::USER_TYPE] == 'Tutor';
Layout::displaySideMenu($isTutor);
?>

<div class="main-view">

    <h2>Available Tutors</h2>

    <table class="tutor-table">
        <tr>
            <th>Tutors</th>
        </tr>
        <?php
            // Gets users of type 'Tutor'
            $tutors = UserRepository::getUsersByType('Tutor');

            // Iterates over tutors
            if ($tutors) {
                foreach ($tutors as $tutor) {
                    // Displays table rows
                    echo "
                        <tr>
                            <td onclick='viewUser({$tutor->getUserId()})'>
                                <div>
                                    <i class='fa-solid fa-user'></i> {$tutor->getFirstName()} {$tutor->getLastName()}
                                </div>
            
                                <div>
                                    <i  class='fa-solid fa-calendar'></i> Joined on: {$tutor->getCreatedAt()->format('d-m-Y')}
                                </div>
                            </td>
                        </tr>
                    ";
                }
            }

        ?>
    </table>


</div>
