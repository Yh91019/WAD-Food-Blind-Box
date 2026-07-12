<?php
session_start();

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

include 'includes/db_connect.php';

$sql = "SELECT * FROM users WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();

$result = $stmt->get_result();
$user = $result->fetch_assoc();

include 'includes/header.php';
include 'includes/navigation.php';
?>

<main class="profile-page">

    <h1>My Profile</h1>

    <div class="profile-card">
        <h2>Personal Details</h2>

        <h3>Account</h3>
        <p><strong>Name:</strong> <?php echo $user['name']; ?></p>
        <p><strong>Email:</strong> <?php echo $user['email']; ?></p>
        <p><strong>Phone Number:</strong> <?php echo $user['phone']; ?></p>
        <p><strong>Address:</strong> <?php echo $user['address']; ?></p>
        <p><strong>Payment Method:</strong> <?php echo $user['paymentmethod']; ?></p>

        <h4>Orders</h4>
        <p><strong>Cart:</strong> <?php echo $user['cart']; ?></p>
        <p><strong>Wishlist:</strong> <?php echo $user['wishlist']; ?></p>
        <p><strong>History:</strong> <?php echo $user['history']; ?></p>

    </div>

</main>

<?php include 'includes/footer.php'; ?>