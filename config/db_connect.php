<?php

$dbhost = "localhost";
$dbUser = "root";
$dbPass = "";
$dbName = "blindbite";
// port: 3306 / 3308
$port = 3308;

// Create connection
$conn = mysqli_connect($dbhost, $dbUser, $dbPass, $dbName, $port);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

?>