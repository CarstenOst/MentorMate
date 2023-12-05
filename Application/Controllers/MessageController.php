<?php

namespace Application\Controllers;

require("../../autoloader.php");


use Infrastructure\Repositories\MessageRepository;
use Application\Constants\SessionConst;
use Application\Validators\Auth;
use Core\Entities\Message;
use PDOException;


// Starts session, and checks if user is logged in. If not, redirects to login page
if (!Auth::checkAuth()) {
    header('Location: ../User/Login.php');
    exit();
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    switch ($_POST['action']) {
        case 'sendMessage':
            $message = new Message(
                messageId: null,
                senderId: $_SESSION[SessionConst::USER_ID],
                receiverId: $_SESSION['chat_last_receiver'],
                sentAt: null,
                messageText: htmlspecialchars($_POST['message']),
                isRead: null
            );
            try {
                MessageRepository::create($message);
            } catch (PDOException $e) {
                echo "Error: ";
            }
            break;

        case 'fetchMessages':
            try {
                $sender = $_SESSION[SessionConst::USER_ID];
                $receiver = $_SESSION['chat_last_receiver'];
                $messages = MessageRepository::getMessagesBetweenUsers($sender, $receiver);
                if ($messages) {
                    foreach ($messages as $message) {
                        if ($message->getSenderId() == $sender) {
                            echo "
                                <div class='message'>
                                    <small><i>{$message->getSentAt()->format('H:m')}</i></small>
                                    <div class='message-content'>
                                        {$message->getMessageText()}
                                    </div>
                                </div>
                                
                            ";
                        } else {
                            echo "
                                <div class='receiver-message'>
                                    <small><i>{$message->getSentAt()->format('H:m')}</i></small>
                                    <div class='receiver-message-content'>
                                        {$message->getMessageText()}
                                    </div>
                                </div>
                            ";
                        }
                    }
                }
            } catch (PDOException $e) {
                die("Error: " . $e->getMessage());
            }
            break;
    }

}
