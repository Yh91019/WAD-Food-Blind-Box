<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blind Bite</title>

    <link rel="stylesheet" href="../css/style.css">
    <?php if (strpos($_SERVER['PHP_SELF'], '/admin/') !== false): ?>

    <link rel="stylesheet" href="../css/admin.css">

    <?php endif; ?>
</head>
<body>