<?php

namespace Application\Controllers\UserController;

require("../../autoloader.php");

use Application\Constants\SessionConst;
use Application\Validators\Auth;
use Application\Functions\ProfileImageHandler;
use Application\Validators\Validator;
use Infrastructure\Repositories\UserRepository;


// Starts session, and checks if user is logged in. If not, redirects to login page
if (!Auth::checkAuth()) {
    header('Location: ../User/Login.php');
    exit();
}

// Check if the logout action is requested
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $response = ['status' => 'error', 'message' => ''];

    switch ($_POST['action']) {
        case 'uploadProfilePicture':
            $message = [];
            if (ProfileImageHandler::uploadImage($_FILES['image'], $_SESSION[SessionConst::USER_ID], $message)) {
                // If the upload is successful
                $response = ['status' => 'success', 'message' => 'File uploaded successfully.'];
            } else {
                // Only set this response if the upload failed
                $response = ['status' => 'error', 'message' => implode('<br>', $message)];
            }
            break;

        case 'updateAbout':
            if (!Validator::isValid(Validator::STRIPPED_TEXT, $_POST['about'])){
                $response = ['status' => 'error', 'message' => 'Not a valid text!'];
                break;
            }
            $aboutText = $_POST['about']; // This should be clean at this point

            // Update the about section in the database
            $updateResult = UserRepository::updateAbout($_SESSION[SessionConst::USER_ID], $aboutText);

            // If it is successful, update the session variable, and set a success message
            if ($updateResult) {
                $response = ['status' => 'success', 'message' => 'About section updated successfully.'];
                $_SESSION[SessionConst::ABOUT] = $aboutText;
            } else {
                $response = ['status' => 'error', 'message' => 'Failed to update about section.'];
            }
            break;
    }
    // Send the response to the client (this is captured in the ajax call)
    echo json_encode($response);
}
