<?php
session_start();

if(!isset($_SESSION['username'])){
    header("Location: login.php");
    exit();
}

include '../config/db_connect.php';

$username = $_SESSION['username'];
$error = "";
$success = "";

// Show a one-time success message after a redirect (Post/Redirect/Get)
if (isset($_SESSION['profile_message'])) {
    $success = $_SESSION['profile_message'];
    unset($_SESSION['profile_message']);
}

// Handle profile update submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $phone_number   = trim($_POST['phone_number']);
    $address        = trim($_POST['address']);
    $cardholder_name = trim($_POST['cardholder_name']);
    $card_number    = trim($_POST['card_number']);
    $expiry_date    = trim($_POST['expiry_date']);
    $cvv            = trim($_POST['cvv']);

    if ($phone_number === "" || $address === "") {

        $error = "Phone number and address cannot be empty.";

    } else {

        // Make sure the phone number isn't already used by another account
        $check = $conn->prepare("SELECT username FROM users WHERE phone_number = ? AND username != ?");
        $check->bind_param("ss", $phone_number, $username);
        $check->execute();

        if ($check->get_result()->num_rows > 0) {

            $error = "That phone number is already in use by another account.";

        } else {

            // Update account details
            $stmt = $conn->prepare("UPDATE users SET phone_number = ?, address = ? WHERE username = ?");
            $stmt->bind_param("sss", $phone_number, $address, $username);
            $stmt->execute();
            $stmt->close();

            // Update payment method only if the user filled in the fields
            if ($cardholder_name !== "" || $card_number !== "" || $expiry_date !== "" || $cvv !== "") {

                $existing = $conn->prepare("SELECT payment_id FROM paymentmethod WHERE username = ?");
                $existing->bind_param("s", $username);
                $existing->execute();
                $existingResult = $existing->get_result();

                if ($existingResult->num_rows > 0) {

                    $pm = $conn->prepare("UPDATE paymentmethod
                                           SET cardholder_name = ?, card_number = ?, expiry_date = ?, cvv = ?
                                           WHERE username = ?");
                    $pm->bind_param("sssss", $cardholder_name, $card_number, $expiry_date, $cvv, $username);
                    $pm->execute();
                    $pm->close();

                } else {

                    $pm = $conn->prepare("INSERT INTO paymentmethod (username, cardholder_name, card_number, expiry_date, cvv)
                                           VALUES (?, ?, ?, ?, ?)");
                    $pm->bind_param("sssss", $username, $cardholder_name, $card_number, $expiry_date, $cvv);
                    $pm->execute();
                    $pm->close();

                }
            }

            $_SESSION['profile_message'] = "Profile updated successfully!";
            header("Location: profile.php");
            exit();
        }
    }
}

// Fetch user account details
$sql = "SELECT * FROM users WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Fetch saved payment method (if any)
$pmStmt = $conn->prepare("SELECT * FROM paymentmethod WHERE username = ?");
$pmStmt->bind_param("s", $username);
$pmStmt->execute();
$paymentMethod = $pmStmt->get_result()->fetch_assoc();
$pmStmt->close();

$conn->close();

// Reopen the edit panel automatically if the form was submitted with an error
$showEditOnLoad = !empty($error);

include '../includes/header.php';
include '../includes/navigation.php';
?>

<main class="profile-page" data-show-edit="<?php echo $showEditOnLoad ? '1' : '0'; ?>">

    <h1>My Profile</h1>

    <?php if (!empty($error)) : ?>
        <p class="error"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>

    <?php if (!empty($success)) : ?>
        <p class="success"><?php echo htmlspecialchars($success); ?></p>
    <?php endif; ?>

        <div id="profile-view" class="profile-card">
            <h2>Personal Details</h2>

            <h3>Account</h3>
            <p><strong>Name:</strong> <?php echo htmlspecialchars($user['username']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
            <p><strong>Phone Number:</strong> <?php echo htmlspecialchars($user['phone_number']); ?></p>
            <p><strong>Address:</strong> <?php echo htmlspecialchars($user['address']); ?></p>

            <h3>Payment Method</h3>
            <?php if ($paymentMethod) : ?>
                <p><strong>Cardholder Name:</strong> <?php echo htmlspecialchars($paymentMethod['cardholder_name']); ?></p>
                <p><strong>Card Number:</strong> **** **** **** <?php echo htmlspecialchars(substr($paymentMethod['card_number'], -4)); ?></p>
                <p><strong>Expiry Date:</strong> <?php echo htmlspecialchars($paymentMethod['expiry_date']); ?></p>
            <?php else : ?>
                <p>No payment method saved yet.</p>
            <?php endif; ?>

            <button type="button" id="edit-profile-btn" class="edit-profile-btn">Edit Profile</button>
        </div>

        <div id="profile-edit" class="profile-card" hidden>
            <h2>Edit Profile</h2>

            <form method="POST" action="profile.php">

                <h3>Account</h3>

                <label for="phone_number">Phone Number</label><br>
                <input
                    type="text"
                    id="phone_number"
                    name="phone_number"
                    value="<?php echo htmlspecialchars($user['phone_number']); ?>"
                    required>

                <br><br>

                <label for="address">Address</label><br>
                <textarea id="address" name="address" rows="3" required><?php echo htmlspecialchars($user['address']); ?></textarea>

                <br><br>

                <h3>Payment Method</h3>
                <p class="field-hint">Leave these fields blank to keep your payment method unchanged.</p>

                <label for="cardholder_name">Cardholder Name</label><br>
                <input
                    type="text"
                    id="cardholder_name"
                    name="cardholder_name"
                    value="<?php echo $paymentMethod ? htmlspecialchars($paymentMethod['cardholder_name']) : ''; ?>">

                <br><br>

                <label for="card_number">Card Number</label><br>
                <input
                    type="text"
                    id="card_number"
                    name="card_number"
                    maxlength="20"
                    value="<?php echo $paymentMethod ? htmlspecialchars($paymentMethod['card_number']) : ''; ?>">

                <br><br>

                <label for="expiry_date">Expiry Date (MM/YY)</label><br>
                <input
                    type="text"
                    id="expiry_date"
                    name="expiry_date"
                    placeholder="MM/YY"
                    maxlength="10"
                    value="<?php echo $paymentMethod ? htmlspecialchars($paymentMethod['expiry_date']) : ''; ?>">

                <br><br>

                <label for="cvv">CVV</label><br>
                <input
                    type="password"
                    id="cvv"
                    name="cvv"
                    maxlength="4">

                <br><br>

                <button type="submit">Save Changes</button>
                <button type="button" id="cancel-edit-btn" class="cancel-btn">Cancel</button>

            </form>
        </div>

    <script src="../js/script.js"></script>
    <script src="../js/profile.js"></script>

</main>

<?php include '../includes/footer.php'; ?>
