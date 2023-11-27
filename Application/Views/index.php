<?php
namespace  Application\Views\User;


use Application\Views\Shared\HtmlRenderer;
use Application\Views\Shared\Layout;

require("../../autoloader.php");


?>
<head>
    <link rel="stylesheet" href="/Assets/style.css">
    <script src="https://kit.fontawesome.com/5928831ae4.js" crossorigin="anonymous"></script>
</head>

<body>

    <div class='custom-container'>
        <div>
            <div>
                <h2>MentorMate</h2>
                <br>
                <h4>What do you want to do?</h4>
                <br>
                <a href='../Views/Book/index.php' class='custom-img-link'><img width='160' height='160' src='../Assets/book.svg' alt='Book session'></a>
                <a href='../Views/Bookings/index.php' class='custom-img-link'><img width='160' height='160' src='../Assets/bookings.svg' alt='Your booked sessions'></a>
                <a href='../Views/Messages/index.php' class='custom-img-link'><img width='160' height='160' src='../Assets/messages.svg' alt='Your messages'></a>
                <a href='../Views/User/Profile.php' class='custom-img-link'><img width='160' height='160' src='../Assets/profile.svg' alt='Your profile'></a>
            </div>
        </div>
    </div>

</body>
