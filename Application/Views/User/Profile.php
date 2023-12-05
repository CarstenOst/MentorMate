<?php

namespace Application\Views\User;

require ("../../../autoloader.php");

use Application\Functions\ProfileImageHandler;
use Application\Validators\Auth;
use Application\Views\Shared\Layout;
use Application\Constants\SessionConst;

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

class Profile
{
    /**
     * View the user profile if the user is logged-in
     * Session must be set!
     *
     * @return void echos the user profile
     */
    public static function viewUserProfile(): void
    {
        $imageFileName = $_SESSION[SessionConst::USER_ID];
        $firstName = $_SESSION[SessionConst::FIRST_NAME];
        $lastName = $_SESSION[SessionConst::LAST_NAME];
        $userType = $_SESSION[SessionConst::USER_TYPE];
        $email = $_SESSION[SessionConst::EMAIL];
        $about = $_SESSION[SessionConst::ABOUT] == '' ? 'Bio in progress...' : $_SESSION[SessionConst::ABOUT];
        $about = htmlspecialchars($about); // Prevent XSS (Cross-site scripting

        $fileExtension = ProfileImageHandler::getFileExtensionByFileName($imageFileName) ?? 'svg';

        if (file_exists("../../ProfileImages/$imageFileName.$fileExtension")) {
            $fileExtension = ProfileImageHandler::getFileExtensionByFileName($imageFileName);
        } else {
            $imageFileName = 'profile';
            $fileExtension = 'svg';
        }


        echo "
            <div class='profile-container'>
                 <img src='../../ProfileImages/$imageFileName.$fileExtension' alt='Tutors Profile Picture' id='profileImage'>
                <input type='file' id='imageUpload' name='image' style='display: none;'>
                <h1 class='tutor-name'>$firstName $lastName</h1>
                <p class='user-type'>$userType</p>
        
                <div class='about'>
                    <h2>About <span id='firstNameDisplay'>$firstName</span></h2>
                    <div id='aboutText' class='editable-text' contenteditable='true'>$about</div>
                    <textarea id='aboutInput' class='editable-input' rows='4' style='display:none;'></textarea>
                </div>

        
                <div class='contact-info'>
                    <h2>Contact Information</h2>
                    <p><b>Email:</b> $email</p>
                    <!-- <button class='message-button'>Message $userType</button> -->
                </div>
        ";
    }

}
?>

<head>
    <link rel="stylesheet" href="../../Assets/style.css">
    <script src="https://kit.fontawesome.com/5928831ae4.js" crossorigin="anonymous"></script>
</head>

<body>
    <?php
        $isTutor = $_SESSION[SessionConst::USER_TYPE] == 'Tutor';
        Layout::displaySideMenu($isTutor);
    ?>

    <div class="main-view">
        <?php
            Profile::viewUserProfile();
        ?>
    </div>
    <div id="popupMessage"
         style="
         display:none; position: fixed; bottom: 20px; right: 20px;
         background-color: black; color: white; padding: 10px; border-radius: 5px;"></div>

</body>
<script>
    function showPopupMessage(message, isSuccess) {
        let popup = document.getElementById('popupMessage');
        popup.textContent = message;
        popup.style.backgroundColor = isSuccess ? "green" : "red"; // Success: green, Error: red
        popup.style.display = "block";

        // Start the fade-out effect after a delay
        setTimeout(function() {
            popup.classList.add("fadeOut");

            // Hide the popup after the fade-out effect
            setTimeout(function() {
                popup.style.display = "none";
                popup.classList.remove("fadeOut");
            }, 3000);
        }, 2000);
    }

    // AJAX!

    document.getElementById('profileImage').addEventListener('click', function() {
        document.getElementById('imageUpload').click();
    });

    document.getElementById('imageUpload').addEventListener('change', function() {
        if (this.files && this.files[0]) {
            // Prepare the FormData for the AJAX request
            let formData = new FormData();
            formData.append('image', this.files[0]);
            formData.append('action', 'uploadProfilePicture');

            let xhr = new XMLHttpRequest();
            xhr.onload = function() {
                if (xhr.status === 200) {
                    let response = JSON.parse(xhr.responseText);
                    if (response.status === 'success') {
                        showPopupMessage(response.message, true); // Using the message from the server

                        // Update the profile image src to display the uploaded image
                        var reader = new FileReader();
                        reader.onload = function(e) {
                            document.getElementById('profileImage').setAttribute('src', e.target.result);
                        };
                        reader.readAsDataURL(document.getElementById('imageUpload').files[0]);

                    } else {
                        showPopupMessage(response.message, false);
                    }
                } else {
                    showPopupMessage('Request failed. Returned status of ' + xhr.status, false);
                }
            };

            xhr.open('POST', '../../Controllers/UserController.php', true);
            xhr.send(formData);
        }
    });

    document.getElementById('aboutText').addEventListener('click', function() {
        let aboutTextElement = this;
        let aboutInput = document.getElementById('aboutInput');

        // Set the textarea's value and show it
        aboutInput.value = aboutTextElement.innerText;
        aboutInput.style.display = 'block';
        aboutTextElement.style.display = 'none';

        // Focus the textarea
        aboutInput.focus();
    });

    document.getElementById('aboutInput').addEventListener('blur', function() {
        let aboutInput = this;
        let aboutTextElement = document.getElementById('aboutText');

        // Prepare the FormData for the AJAX request
        let formData = new FormData();
        formData.append('about', aboutInput.value);
        formData.append('action', 'updateAbout');

        let xhr = new XMLHttpRequest();
        xhr.onload = function() {
            if (xhr.status === 200) {
                let response = JSON.parse(xhr.responseText);
                if (response.status === 'success') {
                    showPopupMessage(response.message, true);
                    aboutTextElement.innerText = aboutInput.value;
                } else {
                    showPopupMessage(response.message, false);
                }
                aboutInput.style.display = 'none';
                aboutTextElement.style.display = 'block';
            } else {
                showPopupMessage('Request failed. Returned status of ' + xhr.status, false);
            }
        };

        xhr.open('POST', '../../Controllers/UserController.php', true);
        xhr.send(formData);

        aboutInput.style.display = 'none';
        aboutTextElement.innerText = aboutInput.value;
        aboutTextElement.style.display = 'block';
    });
</script>
