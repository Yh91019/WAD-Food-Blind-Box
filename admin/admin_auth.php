<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Call this at the top of any admin-only page to block non-admins.
function require_admin_login() {

    if (empty($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {

        header("Location: ../authentication/login.php");
        exit();

    }

}

?>