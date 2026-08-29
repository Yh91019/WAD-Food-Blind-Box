<?php



if (session_status() === PHP_SESSION_NONE) {

    session_start();

}



if (empty($_SESSION['username'])) {

    header(
        "Location: ../authentication/login.php"
    );

    exit();

}

if (
    !isset($_SESSION['user_type'])
    ||
    $_SESSION['user_type'] !== 'REVIEWER'
) {

    header(
        "Location: ../index.php"
    );

    exit();

}



if (
    isset($_SESSION['is_admin'])
    &&
    $_SESSION['is_admin'] === true
) {

    unset($_SESSION['is_admin']);

    header(
        "Location: ../index.php"
    );

    exit();

}

?>