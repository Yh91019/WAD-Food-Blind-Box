<?php
$id = $_GET['id'];

// Example data (later you'll get this from the database)
if ($id == 1) {
    $name = "Reastaurant Name";
    $rating = "4.8 ★";
    $description = "RICE(fixed), 6C3:fried chicken, curry chicken, vegetables, tofu, egg, or fish.";
    $phone = "012-3456789";
    $address = " ";
}
elseif ($id == 2) {
    $name = "Reastaurant Name";
    $rating = "4.6 ★";
    $description = "Blind box contains mixed rice with 3 randomly selected dishes.";
    $phone = "017-9876543";
    $address = " ";
}
else {
    $name = "Unknown Restaurant";
    $rating = "-";
    $description = "-";
    $phone = "-";
    $address = "-";
}

include 'includes/header.php';
include 'includes/navigation.php';
?>

<main class="details-page">

    <div class="details-card">

        <img src="images/BBbox.png" width="200" height="150">
            <h1><?php echo $name; ?></h1>
            <p><strong>Rating:</strong> <?php echo $rating; ?></p>
            <p><strong>Blind Box Description:</strong> <?php echo $description; ?></p>
            <p><strong>Contact Number:</strong> <?php echo $phone; ?></p>
            <p><strong>Address:</strong> <?php echo $address; ?></p>

    </div>

</main>

<?php include 'includes/footer.php'; ?>