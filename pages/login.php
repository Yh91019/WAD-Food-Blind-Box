<?php
session_start();
include 'config/db_connect.php';

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Find user by email
    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        die("Database error: " . $conn->error);
    }

    $stmt->bind_param("s", $email);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows == 1) {

        $user = $result->fetch_assoc();

        // Verify hashed password
        if (password_verify($password, $user['password'])) {

            // Store user session
            $_SESSION['username'] = $user['username'];
            $_SESSION['email'] = $user['email'];

            $_SESSION['login_message'] = "Welcome back, " . $user['username'] . "!";

            // Redirect after successful login
            header("Location: index.php");
            exit();

        } else {

            $error = "Incorrect password.";

        }

    } else {

        $error = "Email has not been registered.";

    }

    $stmt->close();
}

$conn->close();
?>

<?php include '../includes/header.php'; ?>
<?php include '../includes/navigation.php'; ?>

<main class="login-page">

    <div class="login-box">

        <h1>Login</h1>

        <?php if (!empty($error)) : ?>
            <p class="error"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>

        <form method="POST" action="">

            <label for="email">Email</label><br>
            <input
                type="email"
                id="email"
                name="email"
                value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
                required>

            <br><br>

            <label for="password">Password</label><br>
            <input
                type="password"
                id="password"
                name="password"
                required>

            <br><br>

            <button type="submit">Login</button>

            <p class="signup-link">
                Don't have an account?
                <a href="signup.php">Sign Up Now</a>
            </p>

        </form>

    </div>

</main>

<?php include '../includes/footer.php'; ?>