<?php
include '../config/db_connect.php';

$restaurant_name = isset($_GET['restaurant']) ? $_GET['restaurant'] : '';

$sql = "SELECT * FROM restaurants WHERE restaurant_name = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $restaurant_name);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $restaurant = $result->fetch_assoc();
} else {
    $restaurant = null;
}

$stmt->close();
$conn->close();

include '../includes/header.php';
include '../includes/navigation.php';
?>

<main class="details-page">

    <div class="details-card">

        <img src="../images/BBbox.png" width="200" height="150">

        <?php if ($restaurant) : ?>

            <h1><?php echo htmlspecialchars($restaurant['restaurant_name']); ?></h1>
            <p><strong>Opening Hours:</strong> <?php echo htmlspecialchars($restaurant['restaurant_opening_hours']); ?></p>
            <p><strong>Blind Box Description:</strong> <?php echo htmlspecialchars($restaurant['blind_box_description']); ?></p>
            <p><strong>Blind Box Price:</strong> RM <?php echo number_format($restaurant['blind_box_price'], 2); ?></p>
            <p><strong>Remaining Quantity:</strong> <?php echo htmlspecialchars($restaurant['blind_box_remaining_quantity']); ?> Boxes</p>
            <p><strong>Food Category:</strong> <?php echo htmlspecialchars($restaurant['blind_box_food_category']); ?></p>
            <p><strong>Contact Number:</strong> <?php echo htmlspecialchars($restaurant['restaurant_phone_number']); ?></p>
            <p><strong>Address:</strong> <?php echo htmlspecialchars($restaurant['restaurant_address']); ?></p>

        <?php else : ?>

            <h1>Restaurant not found</h1>

        <?php endif; ?>

    </div>

    <script src="../js/script.js"></script>

</main>

<?php include '../includes/footer.php'; ?>