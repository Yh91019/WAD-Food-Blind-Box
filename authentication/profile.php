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

// Load the current details before processing the form.
$stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

$pmStmt = $conn->prepare("SELECT * FROM paymentmethod WHERE username = ?");
$pmStmt->bind_param("s", $username);
$pmStmt->execute();
$paymentMethod = $pmStmt->get_result()->fetch_assoc();
$pmStmt->close();

$activeVouchers = [];
$pastVouchers = [];
$voucherStmt = $conn->prepare(
    "SELECT
        promotions.code,
        promotions.title,
        promotions.description,
        promotions.discount_type,
        promotions.discount_value,
        promotions.minimum_spend,
        promotions.starts_at,
        promotions.ends_at,
        promotions.is_active,
        user_vouchers.claimed_at,
        user_vouchers.used_at,
        CASE
            WHEN user_vouchers.used_at IS NOT NULL THEN 'Used'
            WHEN promotions.is_active = 0 OR NOW() > promotions.ends_at THEN 'Expired'
            WHEN NOW() < promotions.starts_at THEN 'Upcoming'
            ELSE 'Active'
        END AS voucher_status
     FROM user_vouchers
     INNER JOIN promotions
        ON promotions.promotion_id = user_vouchers.promotion_id
     WHERE user_vouchers.username = ?
     ORDER BY user_vouchers.claimed_at DESC"
);
$voucherStmt->bind_param('s', $username);
$voucherStmt->execute();
$voucherResult = $voucherStmt->get_result();
while ($voucher = $voucherResult->fetch_assoc()) {
    if ($voucher['voucher_status'] === 'Active') {
        $activeVouchers[] = $voucher;
    } else {
        $pastVouchers[] = $voucher;
    }
}
$voucherStmt->close();

// Show a one-time success message after a redirect (Post/Redirect/Get)
if (isset($_SESSION['profile_message'])) {
    $success = $_SESSION['profile_message'];
    unset($_SESSION['profile_message']);
}

if (isset($_SESSION['profile_error'])) {
    $error = $_SESSION['profile_error'];
    unset($_SESSION['profile_error']);
}

// Handle profile update submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $phone_number    = trim($_POST['phone_number'] ?? '');
    $address         = trim($_POST['address'] ?? '');
    $cardholder_name = trim($_POST['cardholder_name'] ?? '');
    $card_number     = preg_replace('/[\s-]+/', '', trim($_POST['card_number'] ?? ''));
    $expiry_date     = trim($_POST['expiry_date'] ?? '');
    $cvv             = trim($_POST['cvv'] ?? '');
    $has_payment_details = $cardholder_name !== '' || $card_number !== '' ||
                           $expiry_date !== '' || $cvv !== '';

    if ($phone_number === "" || $address === "") {

        $error = "Phone number and address cannot be empty.";

    } elseif (!preg_match('/^01[0-9]{8,9}$/', $phone_number)) {

        $error = "Enter a valid Malaysian phone number, for example 0123456789.";

    } elseif (strlen($address) < 5 || strlen($address) > 255) {

        $error = "Address must be between 5 and 255 characters.";

    } elseif ($has_payment_details &&
              ($cardholder_name === '' || $card_number === '' || $expiry_date === '')) {

        $error = "Complete all payment fields, or leave all of them blank.";

    } elseif ($has_payment_details &&
              !preg_match("/^[\\p{L} .'-]{2,100}$/u", $cardholder_name)) {

        $error = "Cardholder name contains invalid characters.";

    } elseif ($has_payment_details && !preg_match('/^[0-9]{13,19}$/', $card_number)) {

        $error = "Card number must contain 13 to 19 digits.";

    } elseif ($has_payment_details && !preg_match('/^(0[1-9]|1[0-2])\/([0-9]{2})$/', $expiry_date, $expiry_parts)) {

        $error = "Expiry date must use MM/YY format.";

    } elseif ($has_payment_details &&
              ((int) ('20' . $expiry_parts[2]) < (int) date('Y') ||
              ((int) ('20' . $expiry_parts[2]) === (int) date('Y') &&
               (int) $expiry_parts[1] < (int) date('n')))) {

        $error = "The card has expired.";

    } elseif ($has_payment_details && $cvv === '' && !$paymentMethod) {

        $error = "CVV is required for a new payment method.";

    } elseif ($has_payment_details && $cvv !== '' && !preg_match('/^[0-9]{3,4}$/', $cvv)) {

        $error = "CVV must contain 3 or 4 digits.";

    } else {

        // Make sure the phone number isn't already used by another account
        $check = $conn->prepare("SELECT username FROM users WHERE phone_number = ? AND username != ?");
        $check->bind_param("ss", $phone_number, $username);
        $check->execute();
        $phone_in_use = $check->get_result()->num_rows > 0;
        $check->close();

        if ($phone_in_use) {

            $error = "That phone number is already in use by another account.";

        } else {

            // Update account details
            $stmt = $conn->prepare("UPDATE users SET phone_number = ?, address = ? WHERE username = ?");
            $stmt->bind_param("sss", $phone_number, $address, $username);
            $stmt->execute();
            $stmt->close();

            // A blank CVV keeps the saved value when other card details change.
            if ($has_payment_details) {

                $cvv_to_save = $cvv !== '' ? $cvv : $paymentMethod['cvv'];

                $existing = $conn->prepare("SELECT payment_id FROM paymentmethod WHERE username = ?");
                $existing->bind_param("s", $username);
                $existing->execute();
                $existingResult = $existing->get_result();

                if ($existingResult->num_rows > 0) {

                    $pm = $conn->prepare("UPDATE paymentmethod
                                           SET cardholder_name = ?, card_number = ?, expiry_date = ?, cvv = ?
                                           WHERE username = ?");
                    $pm->bind_param("sssss", $cardholder_name, $card_number, $expiry_date, $cvv_to_save, $username);
                    $pm->execute();
                    $pm->close();

                } else {

                    $pm = $conn->prepare("INSERT INTO paymentmethod (username, cardholder_name, card_number, expiry_date, cvv)
                                           VALUES (?, ?, ?, ?, ?)");
                    $pm->bind_param("sssss", $username, $cardholder_name, $card_number, $expiry_date, $cvv_to_save);
                    $pm->execute();
                    $pm->close();

                }
            }

            $_SESSION['profile_message'] = "Profile updated successfully!";
            header("Location: profile.php");
            exit();
        }
    }

    // Keep valid entries visible when another field needs correction.
    if ($error !== '') {
        $user['phone_number'] = $phone_number;
        $user['address'] = $address;
        $paymentMethod = [
            'cardholder_name' => $cardholder_name,
            'card_number' => $card_number,
            'expiry_date' => $expiry_date,
        ];
    }
}

$conn->close();

// Reopen the edit panel automatically if the form was submitted with an error
$showEditOnLoad = !empty($error);

include '../includes/header.php';
include '../includes/navigation.php';
?>

<link rel="stylesheet" href="../css/profile.css">

<main class="profile-page" data-show-edit="<?php echo $showEditOnLoad ? '1' : '0'; ?>">

    <h1>My Profile</h1>

    <?php if (!empty($error)) : ?>
        <p class="error"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>

    <?php if (!empty($success)) : ?>
        <p class="success"><?php echo htmlspecialchars($success); ?></p>
    <?php endif; ?>

    <div class="profile-overview">
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

        <section id="profile-vouchers" class="voucher-wallet-card" aria-labelledby="voucherWalletTitle">
            <div class="voucher-wallet-header">
                <div>
                    <span class="voucher-wallet-kicker">My rewards</span>
                    <h2 id="voucherWalletTitle">Voucher Wallet</h2>
                </div>
                <span class="voucher-count">
                    <?php echo count($activeVouchers); ?> remaining
                </span>
            </div>

            <div class="voucher-tabs" role="tablist" aria-label="Voucher categories">
                <button
                    type="button"
                    class="voucher-tab-btn is-active"
                    id="activeVoucherTab"
                    role="tab"
                    aria-selected="true"
                    aria-controls="activeVoucherPanel">
                    Active Vouchers
                    <span><?php echo count($activeVouchers); ?></span>
                </button>
                <button
                    type="button"
                    class="voucher-tab-btn"
                    id="pastVoucherTab"
                    role="tab"
                    aria-selected="false"
                    aria-controls="pastVoucherPanel">
                    Past Vouchers
                    <span><?php echo count($pastVouchers); ?></span>
                </button>
            </div>

            <div class="voucher-tab-panels">
                <div
                    class="voucher-column voucher-tab-panel active-voucher-column"
                    id="activeVoucherPanel"
                    role="tabpanel"
                    aria-labelledby="activeVoucherTab">

                    <?php if (empty($activeVouchers)) : ?>
                        <div class="voucher-empty">
                            <span aria-hidden="true">🎟️</span>
                            <p>No active vouchers.</p>
                            <a href="../index.php#promotions">View promotions</a>
                        </div>
                    <?php else : ?>
                        <div class="profile-voucher-list">
                            <?php foreach ($activeVouchers as $voucher) : ?>
                                <article class="profile-voucher-item voucher-active">
                                    <div class="profile-voucher-topline">
                                        <strong><?php echo htmlspecialchars($voucher['code']); ?></strong>
                                        <span>Active</span>
                                    </div>
                                    <h4><?php echo htmlspecialchars($voucher['title']); ?></h4>
                                    <p><?php echo htmlspecialchars($voucher['description']); ?></p>
                                    <small>
                                        Min. spend RM<?php echo number_format((float) $voucher['minimum_spend'], 2); ?>
                                        · Expires <?php echo date('d M Y', strtotime($voucher['ends_at'])); ?>
                                    </small>
                                    <a href="../pages/cart.php">Use voucher →</a>
                                </article>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div
                    class="voucher-column voucher-tab-panel past-voucher-column"
                    id="pastVoucherPanel"
                    role="tabpanel"
                    aria-labelledby="pastVoucherTab"
                    hidden>
                    <?php if (empty($pastVouchers)) : ?>
                        <div class="voucher-empty">
                            <span aria-hidden="true">🕘</span>
                            <p>No used or expired vouchers yet.</p>
                        </div>
                    <?php else : ?>
                        <div class="profile-voucher-list">
                            <?php foreach ($pastVouchers as $voucher) : ?>
                                <article class="profile-voucher-item voucher-past">
                                    <div class="profile-voucher-topline">
                                        <strong><?php echo htmlspecialchars($voucher['code']); ?></strong>
                                        <span><?php echo htmlspecialchars($voucher['voucher_status']); ?></span>
                                    </div>
                                    <h4><?php echo htmlspecialchars($voucher['title']); ?></h4>
                                    <p><?php echo htmlspecialchars($voucher['description']); ?></p>
                                    <small>
                                        <?php if ($voucher['used_at']) : ?>
                                            Used <?php echo date('d M Y', strtotime($voucher['used_at'])); ?>
                                        <?php else : ?>
                                            Ended <?php echo date('d M Y', strtotime($voucher['ends_at'])); ?>
                                        <?php endif; ?>
                                    </small>
                                </article>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </section>
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
                    pattern="01[0-9]{8,9}"
                    maxlength="11"
                    inputmode="numeric"
                    title="Enter 10 or 11 digits starting with 01"
                    value="<?php echo htmlspecialchars($user['phone_number']); ?>"
                    required>

                <br><br>

                <label for="address">Address</label><br>
                <textarea id="address" name="address" rows="3" minlength="5" maxlength="255" required><?php echo htmlspecialchars($user['address']); ?></textarea>

                <br><br>

                <h3>Payment Method</h3>
                <p class="field-hint">
                    <?php if ($paymentMethod) : ?>
                        Update the details if needed. Leave CVV blank to keep the saved security code.
                    <?php else : ?>
                        Complete all payment fields to save a payment method.
                    <?php endif; ?>
                </p>

                <label for="cardholder_name">Cardholder Name</label><br>
                <input
                    type="text"
                    id="cardholder_name"
                    name="cardholder_name"
                    minlength="2"
                    maxlength="100"
                    pattern="[A-Za-z .'-]{2,100}"
                    title="Use letters, spaces, apostrophes, full stops, or hyphens"
                    value="<?php echo $paymentMethod ? htmlspecialchars($paymentMethod['cardholder_name']) : ''; ?>">

                <br><br>

                <label for="card_number">Card Number</label><br>
                <input
                    type="text"
                    id="card_number"
                    name="card_number"
                    minlength="13"
                    maxlength="23"
                    inputmode="numeric"
                    pattern="[0-9 -]{13,23}"
                    title="Enter 13 to 19 digits; spaces or hyphens are allowed"
                    value="<?php echo $paymentMethod ? htmlspecialchars($paymentMethod['card_number']) : ''; ?>">

                <br><br>

                <label for="expiry_date">Expiry Date (MM/YY)</label><br>
                <input
                    type="text"
                    id="expiry_date"
                    name="expiry_date"
                    placeholder="MM/YY"
                    maxlength="5"
                    inputmode="numeric"
                    pattern="(0[1-9]|1[0-2])/[0-9]{2}"
                    title="Enter the expiry date as MM/YY"
                    value="<?php echo $paymentMethod ? htmlspecialchars($paymentMethod['expiry_date']) : ''; ?>">

                <br><br>

                <label for="cvv">CVV</label><br>
                <input
                    type="password"
                    id="cvv"
                    name="cvv"
                    minlength="3"
                    maxlength="4"
                    inputmode="numeric"
                    pattern="[0-9]{3,4}"
                    title="Enter the 3 or 4 digit security code">

                <br><br>

                <button type="submit">Save Changes</button>
                <button type="button" id="cancel-edit-btn" class="cancel-btn">Cancel</button>

            </form>
        </div>

    <script src="../js/script.js"></script>
    <script src="../js/profile.js"></script>

</main>

<?php include '../includes/footer.php'; ?>
