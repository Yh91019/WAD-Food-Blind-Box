<?php
session_start();

session_unset();
session_destroy();

session_start();

$_SESSION['logout_message'] = "You have successfully logged out.";

header("Location: ../authentication/login.php");
exit();
?>