<?php

$servername = "autorack.proxy.rlwy.net";
$username = "root";
$password = "nZSXmESUUBlkgkyRfgoSIOXhdLGWZDke";
$dbname = "railway";
$port = 29249;

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname, $port);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "Connected successfully";

?>