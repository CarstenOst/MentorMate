<?php

require("../../../autoloader.php");

use Application\Constants\SessionConst;
use Infrastructure\Repositories\MessageRepository;
use Application\Validators\Auth;
use Application\Views\Shared\Layout;
use Infrastructure\Repositories\UserRepository;


// Starts session, and checks if user is logged in. If not, redirects to login page
if (!Auth::checkAuth()) {
    header('Location: ../User/Login.php');
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


// Checks if the conversation partner userId is correctly set
if ($_SESSION['chat_last_receiver'] === null) {
    // Redirect to previous page if the receiver userId is invalid
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit();
}

?>


<html lang="en">
    <head>
        <link rel="stylesheet" href="../../Assets/style.css">
        <script src="https://kit.fontawesome.com/5928831ae4.js" crossorigin="anonymous"></script>


        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <script>
            $(document).ready(function() {
                // Submit form using Ajax
                $('#chat-form').submit(function(e) {
                    e.preventDefault();
                    let message = $('#message').val();

                    if (message !== '') {
                        $.ajax({
                            type: 'POST',
                            url: '../../Controllers/MessageController.php',
                            data: {
                                action: "sendMessage",
                                message: message,
                            },
                            success: function() {
                                $('#message').val('');
                                fetchMessages();
                            }
                        });
                    }
                });

                // Sends message to receiver
                function sendMessage() {
                    $.ajax({
                        type: 'POST',
                        url: '../../Controllers/MessageController.php',
                        data: {
                            action: "sendMessage",
                            message: message,
                        },
                        success: function() {
                            $('#message').val('');
                            fetchMessages();
                        }
                    });
                }


                // Fetch and display messages
                function fetchMessages(receiverId) {
                    $.ajax({
                        type: 'POST',
                        url: '../../Controllers/MessageController.php',
                        data: {
                            action: "fetchMessages",
                            receiverId: receiverId
                        },
                        success: function(data) {
                            $('#chat-messages').html(data);
                            // Scroll to the bottom of the chat container
                            $('#chat-messages').scrollTop($('#chat-messages')[0].scrollHeight);
                        }
                    });
                }

                // Fetch messages on page load
                fetchMessages();

                // Fetch messages every 5 seconds
                setInterval(fetchMessages, 1000 * 5);
            });
        </script>
    </head>
<body>

    <?php
        $isTutor = $_SESSION[SessionConst::USER_TYPE] === 'Tutor';
        Layout::displaySideMenu($isTutor);
    ?>
    <div class="main-view">


        <div id="chat-container">
            <?php
                $receiver = UserRepository::read($_SESSION['chat_last_receiver']);
                echo "
                    <div class='chat-receiver'>
                        <h2>{$receiver->getFirstName()} {$receiver->getLastName()}</h2>
                    </div>
                ";
            ?>

            <div id="chat-messages">

            </div>
            <form id="chat-form">
                <input type="text" id="message" placeholder="Type your message...">
                <button type="submit">Send</button>
            </form>
        </div>
    <div class="main-view">

</body>
</html>
