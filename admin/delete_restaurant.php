<?php

include 'admin_auth.php';
require_admin_login();

include '../config/db_connect.php';
require_once '../includes/restaurant_image.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['restaurant_name'])) {

    $restaurant_name = trim($_POST['restaurant_name']);
    $image_filename = null;

    $image_stmt = $conn->prepare(
        "SELECT blind_box_image FROM restaurants WHERE restaurant_name = ?"
    );
    $image_stmt->bind_param("s", $restaurant_name);
    $image_stmt->execute();
    $image_row = $image_stmt->get_result()->fetch_assoc();
    $image_stmt->close();

    if ($image_row) {
        $image_filename = $image_row['blind_box_image'];
    }

    $stmt = $conn->prepare("DELETE FROM restaurants WHERE restaurant_name = ?");

    if (!$stmt) {
        die("Database error: " . $conn->error);
    }

    $stmt->bind_param("s", $restaurant_name);

    if ($stmt->execute()) {

        if ($stmt->affected_rows > 0) {
            delete_restaurant_image($image_filename);
            $_SESSION['admin_message'] = "Restaurant \"$restaurant_name\" deleted.";
        } else {
            $_SESSION['admin_error'] = "Restaurant not found.";
        }

    } else {

        $_SESSION['admin_error'] = "Failed to delete restaurant: " . $stmt->error;

    }

    $stmt->close();

}

$conn->close();

header("Location: restaurants.php");
exit();

?>
