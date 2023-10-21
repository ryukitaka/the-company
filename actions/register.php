<?php
include "../classes/User.php";

// Create an object
$user = new User;

// Call the method
$user->store($_POST);
// $_POST holds all the data from views . register.php
/*


$_POSR['first_name'];
$_POSR['last_name'];
$_POSR['username'];
$_POSR['password'];
*/




?>