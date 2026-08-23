<?php
session_start();
include '../config/db_connect.php';

$error = "";
$success = "";
$form = [
    'username' => '',
    'email' => '',
    'gender' => '',
    'date_of_birth' => '',
    'address' => '',
    'phone_number' => '',
];

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $plain_password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $gender = $_POST['gender'] ?? '';
    $dob = $_POST['date_of_birth'] ?? '';
    $address = trim($_POST['address'] ?? '');
    $phone = trim($_POST['phone_number'] ?? '');

    $form = [
        'username' => $username,
        'email' => $email,
        'gender' => $gender,
        'date_of_birth' => $dob,
        'address' => $address,
        'phone_number' => $phone,
    ];

    $dob_date = DateTime::createFromFormat('Y-m-d', $dob);
    $valid_dob = $dob_date && $dob_date->format('Y-m-d') === $dob;

    if ($username === '' || $email === '' || $plain_password === '' ||
        $confirm_password === '' || $gender === '' || $dob === '' ||
        $address === '' || $phone === '') {

        $error = "Please fill in all fields.";

    } elseif (!preg_match('/^[A-Za-z0-9_]{3,20}$/', $username)) {

        $error = "Username must be 3 to 20 letters, numbers, or underscores.";

    } elseif (strlen($email) > 50 || !filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $error = "Enter a valid email address with no more than 50 characters.";

    } elseif (strlen($plain_password) < 8 || strlen($plain_password) > 72 ||
              !preg_match('/[A-Za-z]/', $plain_password) ||
              !preg_match('/[0-9]/', $plain_password)) {

        $error = "Password must be 8 to 72 characters and include a letter and a number.";

    } elseif ($plain_password !== $confirm_password) {

        $error = "Passwords do not match.";

    } elseif (!in_array($gender, ['MALE', 'FEMALE', 'OTHER'], true)) {

        $error = "Select a valid gender.";

    } elseif (!$valid_dob || $dob_date > new DateTime('today')) {

        $error = "Enter a valid date of birth that is not in the future.";

    } elseif (strlen($address) < 5 || strlen($address) > 255) {

        $error = "Address must be between 5 and 255 characters.";

    } elseif (!preg_match('/^01[0-9]{8,9}$/', $phone)) {

        $error = "Enter a valid Malaysian phone number, for example 0123456789.";

    } else {

        $check = $conn->prepare(
            "SELECT username, email, phone_number FROM users
             WHERE username = ? OR email = ? OR phone_number = ? LIMIT 1"
        );
        $check->bind_param("sss", $username, $email, $phone);
        $check->execute();
        $existing_user = $check->get_result()->fetch_assoc();
        $check->close();

        if ($existing_user) {

            if (strcasecmp($existing_user['username'], $username) === 0) {
                $error = "That username is already taken.";
            } elseif (strcasecmp($existing_user['email'], $email) === 0) {
                $error = "That email address is already registered.";
            } else {
                $error = "That phone number is already registered.";
            }

        } else {

            $password = password_hash($plain_password, PASSWORD_DEFAULT);

            $sql = "INSERT INTO users
                    (username, password, email, gender, date_of_birth, address, phone_number)
                    VALUES (?, ?, ?, ?, ?, ?, ?)";

            $stmt = $conn->prepare($sql);

            $stmt->bind_param(
                "sssssss",
                $username,
                $password,
                $email,
                $gender,
                $dob,
                $address,
                $phone
            );

            if ($stmt->execute()) {

                $_SESSION['registration_message'] =
                    "Registration successful! You can now log in.";
                $stmt->close();
                $conn->close();
                header("Location: login.php");
                exit();

            } else {

                $error = "Registration failed. Please try again.";

            }

            $stmt->close();
        }
    }
}
?>

<?php include '../includes/header.php'; ?>
<?php include '../includes/navigation.php'; ?>

<link rel="stylesheet" href="../css/signup.css">

<main class="signup-page">

    <div class="signup-box">

        <h1>Create Account</h1>

        <?php
        if ($error != "") {
            echo "<p class='error'>" . htmlspecialchars($error) . "</p>";
        }

        if ($success != "") {
            echo "<p class='success'>" . htmlspecialchars($success) . "</p>";
        }
        ?>

        <form method="POST">

            <label for="username">Username</label><br>
            <input
                type="text"
                id="username"
                name="username"
                minlength="3"
                maxlength="20"
                pattern="[A-Za-z0-9_]{3,20}"
                title="Use 3 to 20 letters, numbers, or underscores"
                autocomplete="username"
                value="<?php echo htmlspecialchars($form['username']); ?>"
                required>

            <br><br>

            <label for="email">Email</label><br>
            <input
                type="email"
                id="email"
                name="email"
                maxlength="50"
                autocomplete="email"
                value="<?php echo htmlspecialchars($form['email']); ?>"
                required>

            <br><br>

            <label for="password">Password</label><br>
            <input
                type="password"
                id="password"
                name="password"
                minlength="8"
                maxlength="72"
                pattern="(?=.*[A-Za-z])(?=.*[0-9]).{8,72}"
                title="Use 8 to 72 characters with at least one letter and one number"
                autocomplete="new-password"
                required>

            <br><br>

            <label for="confirm_password">Confirm Password</label><br>
            <input
                type="password"
                id="confirm_password"
                name="confirm_password"
                minlength="8"
                maxlength="72"
                autocomplete="new-password"
                required>

            <br><br>

            <label for="gender">Gender</label><br>

            <select id="gender" name="gender" required>
                <option value="">Select Gender</option>
                <option value="MALE" <?php echo $form['gender'] === 'MALE' ? 'selected' : ''; ?>>Male</option>
                <option value="FEMALE" <?php echo $form['gender'] === 'FEMALE' ? 'selected' : ''; ?>>Female</option>
                <option value="OTHER" <?php echo $form['gender'] === 'OTHER' ? 'selected' : ''; ?>>Other</option>
            </select>

            <br><br>

            <label for="date_of_birth">Date of Birth</label><br>
            <input
                type="date"
                id="date_of_birth"
                name="date_of_birth"
                max="<?php echo date('Y-m-d'); ?>"
                value="<?php echo htmlspecialchars($form['date_of_birth']); ?>"
                required>

            <br><br>

            <label for="address">Address</label><br>
            <textarea id="address" name="address" rows="4" minlength="5" maxlength="255" required><?php echo htmlspecialchars($form['address']); ?></textarea>

            <br><br>

            <label for="phone_number">Phone Number</label><br>
            <input
                type="text"
                id="phone_number"
                name="phone_number"
                pattern="01[0-9]{8,9}"
                maxlength="11"
                inputmode="numeric"
                title="Enter 10 or 11 digits starting with 01"
                autocomplete="tel"
                value="<?php echo htmlspecialchars($form['phone_number']); ?>"
                required>

            <br><br>

            <button type="submit">
                Sign Up
            </button>

        </form>

    </div>


</main>

<?php include '../includes/footer.php'; ?>
