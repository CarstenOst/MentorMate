<?php

namespace Application\Functions;

if (file_exists('../../vendor/autoload.php'))
    require '../../vendor/autoload.php';
else {
    echo 'Please run composer install';
    exit;
}


use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

include '../../autoloader.php';

use Application\Constants\Secrets;

// Add this yourself

class SendMail
{
    public static function sendMailTo(
        string $recipientEmail,
        string $subject,
        string $htmlBody,
        string $altBody = ''): bool
    {
        // sanitize input
        if (($recipientEmail = filter_var($recipientEmail, FILTER_VALIDATE_EMAIL)) === false)
            return false;


        $mail = new PHPMailer(true);

        try {
            //Server settings
            $mail->isSMTP();                                     // Set mailer to use SMTP
            $mail->Host = Secrets::HOST_DOMAIN;                  // Specify main and backup SMTP servers
            $mail->SMTPAuth = true;                              // Enable SMTP authentication
            $mail->Username = Secrets::SMTP_USERNAME;            // SMTP username
            $mail->Password = Secrets::SMTP_PASSWORD;            // SMTP password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;  // Enable TLS encryption, `ssl` also accepted
            $mail->Port = 587;                                   // TCP port to connect to
            $mail->SMTPDebug = 3;       // Enable verbose debug output TODO REMOVE before flight


             // for testing purposes only, as it is not recommended at all to disable SSL verification
             $mail->SMTPOptions = array(
                'ssl' => array(
                    'verify_peer' => false, // Man in the middle attack
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                )
            );


            //Recipients
            $mail->setFrom(Secrets::SMTP_USERNAME, 'NoreplyPHP');
            $mail->addAddress($recipientEmail);                 // Add a recipient

            // Content
            $mail->isHTML(true);                         // Set email format to HTML
            $mail->Subject = $subject;
            $mail->Body = $htmlBody;
            $mail->AltBody = $altBody;

            $mail->send();
            return true;
        } catch (Exception $e) {
            return false; // Return false if something went wrong. Handle it elsewhere
        }
    }
}

// Example usage:
// SendMail::sendMailTo('aquulsmurf@gmail.com', 'Test', 'Test');



