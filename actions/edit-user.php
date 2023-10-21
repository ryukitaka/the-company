<?php

    include "../classes/User.php";

    $user = new User;

    $user->update($_POST, $_FILES);

    // $_POST  - holds all the data from the form from views>edit-user.php
    /*
        $_POST['first_name'];
        $_POST['last_name'];
        $_POST['username'];
    */  

    // $_FILES - holds the details of the image like name and the actual image
?>