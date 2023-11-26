<?php

namespace Messages;
require_once("../../../autoloader.php");

use Infrastructure\Repositories\UserRepository;

class SendMsg
{

}

// TODO add session and auth
?>

<head>
    <title>Messages</title> <!-- TODO insert username -->
    <link rel="stylesheet" type="text/css" href="./messageStyle.css">
</head>

<ul id='friend-list'>
    <?php
    $users = UserRepository::getAllUsersByRole('Tutor');
    foreach ($users as $user) {
        $userName = $user->getFirstName() . ' ' . $user->getLastName();
        echo "<li class='friend'>";
        echo "<img src='https://i.imgur.com/nkN3Mv0.jpg' alt='tutor image'/>";
        echo "<div class='name'>$userName</div>
        </li>";
    }
    ?>
</ul>
<div class="container">
  <img src="../../Assets/icons/someone.png" alt="Avatar">
  <p>Hello. How are you today?</p>
  <span class="time-right">11:00</span>
</div>

<div class="container darker">
  <img src="../../Assets/icons/me.png" alt="Avatar" class="right">
  <p>Hey! I'm fine. Thanks for asking!</p>
  <span class="time-left">11:01</span>
</div>

<div class="container">
  <img src="../../Assets/icons/someone.png" alt="Avatar">
  <p>Sweet! So, what do you wanna do today?</p>
  <span class="time-right">11:02</span>
</div>

<div class="container darker">
  <img src="../../Assets/icons/me.png" alt="Avatar" class="right">
  <p>Nah, I dunno. Play soccer.. or learn more coding perhaps?</p>
  <span class="time-left">11:05</span>
</div>


