<?php
$conn = new mysqli("localhost", "root", "", "wadassignment");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>