<?php

// All restaurant opening/closing hours are local Malaysia time, and the
// front-end status badge (js/status.js) reads the visitor's own computer
// clock -- so the server needs to compare against the same timezone,
// otherwise "Open"/"Closed" can disagree between the badge and the
// server-side check that actually allows or blocks adding to the cart.
date_default_timezone_set('Asia/Kuala_Lumpur');

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