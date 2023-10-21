<?php
include "../classes/User.php";

// Create an object
$user = new User;

// Call the method
$user->login($_POST);
// $_POST holds all the data from the form or from the index.php or the login form
/*
$_POSR['first_name'];
$_POSR['last_name'];
*/




?>