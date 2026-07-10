<?php
include 'includes/db_connect.php';

$error = "";
$success = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Check if email already exists
    $check = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($check);
    $stmt->bind_param("s", $email);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows > 0) {

        $error = "Email already registered. Please login.";

    } else {

        // Encrypt password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Insert new user
        $sql = "INSERT INTO users (name, email, password) VALUES (?, ?, ?)";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $name, $email, $hashedPassword);

        if ($stmt->execute()) {

            $success = true;

        } else {

            $error = "Registration failed. Please try again.";

        }
    }
}
?>

<?php include 'includes/header.php'; ?>
<?php include 'includes/navigation.php'; ?>

<main class="signup-page">

    <div class="signup-box">
        <?php if($success){ ?>

        <div class="success-box">
            <h2>✅ Registration Successful!</h2>

            <p>Your account has been created successfully.</p>
            <p>You can now log in using your email and password.</p>

            <form action="login.php" method="get">
                <button type="submit" class="success-btn">
                     Go to Login
                </button>
            </form>
        </div>

        <?php } else { ?>

        <h1>Sign Up</h1>

        <?php
        if($error != ""){
            echo "<p class='error'>$error</p>";
        }
        ?>


        <form method="POST">

            <label>Name</label><br>
            <input type="text" name="name" required>

            <br><br>


            <label>Email</label><br>
            <input type="email" name="email" required>

            <br><br>


            <label>Password</label><br>
            <input type="password" name="password" required>

            <br><br>


            <button type="submit">
                Sign Up
            </button>

        </form>


        <p class='login-link'>
            Already have an account?
            <a href="login.php">Login</a>
        </p>

        <?php } ?>
    </div>

</main>


<?php include 'includes/footer.php'; ?>