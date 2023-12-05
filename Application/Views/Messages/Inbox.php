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

// TODO Make table for viewing list of chats user has with button ?and last message sendt viewable?
?>


<html lang="en">
    <head>
        <link rel="stylesheet" href="../../Assets/style.css">
        <script src="https://kit.fontawesome.com/5928831ae4.js" crossorigin="anonymous"></script>


        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <script>
            // Waits for page to load, then makes functions available globally
            document.addEventListener("DOMContentLoaded", function () {
                window.messageUser = function messageUser(userId) {
                    // Use AJAX to call a PHP controller action
                    $.ajax({
                        type: "POST",
                        url: "../../Controllers/BookingController.php",
                        data: {
                            action: "messageUser",
                            userId: userId
                        },
                        success: function (data) {
                            let response = JSON.parse(data);
                            window.location.href = response.redirect;
                        },
                        error: function (jqXHR, textStatus, errorThrown) {
                            // Directly access the error message without parsing JSON
                            alert("Error: " + jqXHR.responseText);
                        }
                    });
                }
            });
        </script>
    </head>
<body>

    <?php
        $isTutor = $_SESSION[SessionConst::USER_TYPE] === 'Tutor';
        Layout::displaySideMenu($isTutor);
    ?>

    <div class='main-view'>
        <div class='booking-view'>
            <h2>Open Conversations</h2>

            <div class='conversation-list'>
                <?php
                    // Queries for the conversations
                    $conversations = MessageRepository::getUserConversations($_SESSION[SessionConst::USER_ID]);
                    if (sizeof($conversations) == 0) {
                        echo "
                            <p>It appears you have no conversations at this time...</p>
                        ";
                    }

                    // Displays the conversations
                    foreach ($conversations as $conversation) {
                        $hasUnread = $conversation['lastMessageRead'] === 0;
                        $unreadStyling = $hasUnread ? 'unread' : '';
                        $conversationPartnerId = $conversation['conversationPartnerId'];

                        if ($hasUnread) {
                            echo "
                                <div class='conversation' onclick='messageUser($conversationPartnerId)'>
                                    <div class='conversation-details'>
                                        <div>
                                            <i class='fa-solid fa-user'></i> {$conversation['firstName']} {$conversation['lastName']}
                                        </div>
                                        
                                        <div>
                                            <i><small>{$conversation['lastMessage']}</small></i>
                                        </div>
                                    </div>
                                    
                                    <div class='last-message-time'>
                                        {$conversation['lastMessageTime']} <i class='notification fa-solid fa-circle'></i>
                                    </div>
                                </div>
                            ";
                        } else {
                            echo "
                                <div class='conversation' onclick='messageUser($conversationPartnerId)'>
                                    <div class='conversation-details'>
                                        <div>
                                            <i class='fa-solid fa-user'></i> {$conversation['firstName']} {$conversation['lastName']}
                                        </div>
                                        
                                        <div>
                                            <i><small>{$conversation['lastMessage']}</small></i>
                                        </div>
                                    </div>
                                    
                                    <div class='last-message-time'>
                                        {$conversation['lastMessageTime']}
                                    </div>
                                </div>
                            ";
                        }
                    }
                ?>
            </div>
        </div>


    <div class='main-view'>

</body>
</html>
