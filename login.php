<?php
session_start();
include 'includes/db_connect.php';

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows == 1) {

        $user = $result->fetch_assoc();

        // If using password_hash() when registering
        if (password_verify($password, $user['password'])) {

            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['name'] = $user['name'];

            $_SESSION['login_message'] = "Welcome back, " . $user['name'] . "!";

            header("Location: profile.php");
            exit();

        } else {
            $error = "Incorrect password.";
        }

    } else {
        $error = "*Email has not been registered. Please sign up first.*";
    }
}
?>

<?php include 'includes/header.php'; ?>
<?php include 'includes/navigation.php'; ?>

<main class="login-page">

    <div class="login-box">

        <h1>Login</h1>

        <?php
        if($error != ""){
            echo "<p class='error'>$error</p>";
        }
        ?>

        <form method="POST">

            <label>Email</label><br>
            <input type="email" name="email" required>

            <br><br>

            <label>Password</label><br>
            <input type="password" name="password" required>

            <br><br>

            <button type="submit">Login</button>
            <p class='signup-link'>
                Don't have an account? 
                <a href='signup.php'>Sign Up Now</a>
            </p>

        </form>

    </div>

</main>

<?php include 'includes/footer.php'; ?>