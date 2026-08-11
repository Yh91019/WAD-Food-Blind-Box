<?php
session_start();
include '../config/db_connect.php';

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $login_id = trim($_POST['email']);
    $password = $_POST['password'];

    // ----------------------------------------------------------
    // 1) Check the admin table first (admin_username / admin_password)
    // ----------------------------------------------------------
    $admin_sql = "SELECT * FROM admin WHERE admin_username = ?";
    $admin_stmt = $conn->prepare($admin_sql);

    if (!$admin_stmt) {
        die("Database error: " . $conn->error);
    }

    $admin_stmt->bind_param("s", $login_id);
    $admin_stmt->execute();
    $admin_result = $admin_stmt->get_result();

    if ($admin_result->num_rows == 1) {

        $admin = $admin_result->fetch_assoc();
        $stored_admin_password = $admin['admin_password'];

        // Supports both a plain-text password and a password_hash() hash
        if (password_get_info($stored_admin_password)['algo'] !== null) {
            $admin_password_ok = password_verify($password, $stored_admin_password);
        } else {
            $admin_password_ok = hash_equals($stored_admin_password, $password);
        }

        if ($admin_password_ok) {

            $_SESSION['username'] = $admin['admin_username'];
            $_SESSION['is_admin'] = true;

            $admin_stmt->close();
            $conn->close();

            header("Location: ../admin/dashboard.php");
            exit();

        } else {

            $error = "Incorrect password.";

        }

        $admin_stmt->close();

    } else {

        $admin_stmt->close();

        // ----------------------------------------------------------
        // 2) Not an admin username — fall back to the regular users table
        // ----------------------------------------------------------
        $sql = "SELECT * FROM users WHERE username = ? OR email = ?";
        $stmt = $conn->prepare($sql);

        if (!$stmt) {
            die("Database error: " . $conn->error);
        }

        $stmt->bind_param("ss", $login_id, $login_id);
        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows == 1) {

            $user = $result->fetch_assoc();

            // Verify password. Supports properly hashed passwords (password_hash),
            // and also a plain-text password.
            $stored_password = $user['password'];

            if (password_get_info($stored_password)['algo'] !== null) {
                $password_ok = password_verify($password, $stored_password);
            } else {
                $password_ok = hash_equals($stored_password, $password);
            }

            if ($password_ok) {

                // Store user session
                $_SESSION['username'] = $user['username'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['login_message'] = "Welcome back, " . $user['username'] . "!";

                $stmt->close();
                $conn->close();

                // Redirect after successful login
                header("Location: ../index.php");
                exit();

            } else {

                $error = "Incorrect password.";

            }

        } else {

            $error = "Username/email has not been registered.";

        }

        $stmt->close();
    }
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

            <label for="email">Username or Email</label><br>
            <input
                type="text"
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

    <script src="../js/script.js"></script>
    
</main>

<?php include '../includes/footer.php'; ?>