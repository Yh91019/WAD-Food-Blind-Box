<?php

session_start();

include '../config/db_connect.php';

$error = "";


/* ============================================================
   LOGIN PROCESS
   ============================================================ */

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $login_id = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';


    /* ========================================================
       FORM VALIDATION
       ======================================================== */

    if ($login_id === "" && $password === "") {

        $error = "Please enter your username/email and password.";

    } elseif ($login_id === "") {

        $error = "Please enter your username or email.";

    } elseif ($password === "") {

        $error = "Please enter your password.";

    } elseif (strlen($login_id) > 100) {

        $error = "Username or email is too long.";

    } else {


        /* ====================================================
           1. CHECK ADMIN TABLE
           ==================================================== */

        $admin_sql = "
            SELECT *
            FROM admin
            WHERE admin_username = ?
        ";

        $admin_stmt = $conn->prepare($admin_sql);


        if (!$admin_stmt) {

            die("Database error: " . $conn->error);

        }


        $admin_stmt->bind_param("s", $login_id);

        $admin_stmt->execute();

        $admin_result = $admin_stmt->get_result();


        /* ====================================================
           ADMIN FOUND
           ==================================================== */

        if ($admin_result->num_rows == 1) {

            $admin = $admin_result->fetch_assoc();

            $stored_admin_password =
                $admin['admin_password'];


            /*
             * Supports both:
             * 1. Plain-text password
             * 2. password_hash() password
             */

            if (
                password_get_info(
                    $stored_admin_password
                )['algo'] !== null
            ) {

                $admin_password_ok =
                    password_verify(
                        $password,
                        $stored_admin_password
                    );

            } else {

                $admin_password_ok =
                    hash_equals(
                        $stored_admin_password,
                        $password
                    );

            }


            /* ================================================
               ADMIN PASSWORD CORRECT
               ================================================ */

            if ($admin_password_ok) {

                $_SESSION['username'] =
                    $admin['admin_username'];

                $_SESSION['is_admin'] = true;


                $admin_stmt->close();

                $conn->close();


                header(
                    "Location: ../admin/dashboard.php"
                );

                exit();

            }


            /* ================================================
               ADMIN PASSWORD WRONG
               ================================================ */

            else {

                $error = "Incorrect password.";

            }


            $admin_stmt->close();

        }


        /* ====================================================
           2. CHECK NORMAL USERS TABLE
           ==================================================== */

        else {

            $admin_stmt->close();


            $sql = "
                SELECT *
                FROM users
                WHERE username = ?
                OR email = ?
            ";


            $stmt = $conn->prepare($sql);


            if (!$stmt) {

                die("Database error: " . $conn->error);

            }


            $stmt->bind_param(
                "ss",
                $login_id,
                $login_id
            );


            $stmt->execute();

            $result = $stmt->get_result();


            /* ================================================
               USER FOUND
               ================================================ */

            if ($result->num_rows == 1) {

                $user = $result->fetch_assoc();


                $stored_password =
                    $user['password'];


                /*
                 * Supports both:
                 * 1. password_hash()
                 * 2. Plain-text password
                 */

                if (
                    password_get_info(
                        $stored_password
                    )['algo'] !== null
                ) {

                    $password_ok =
                        password_verify(
                            $password,
                            $stored_password
                        );

                } else {

                    $password_ok =
                        hash_equals(
                            $stored_password,
                            $password
                        );

                }


                /* ============================================
                   USER PASSWORD CORRECT
                   ============================================ */

                if ($password_ok) {

                    $_SESSION['username'] =
                        $user['username'];

                    $_SESSION['email'] =
                        $user['email'];


                    $_SESSION['login_message'] =
                        "Welcome back, " .
                        $user['username'] .
                        "!";


                    $stmt->close();

                    $conn->close();


                    header(
                        "Location: ../index.php"
                    );

                    exit();

                }


                /* ============================================
                   USER PASSWORD WRONG
                   ============================================ */

                else {

                    $error = "Incorrect password.";

                }

            }


            /* ================================================
               USER NOT FOUND
               ================================================ */

            else {

                $error =
                    "Username/email has not been registered.";

            }


            $stmt->close();

        }

    }

}


/* ============================================================
   CLOSE DATABASE CONNECTION
   ============================================================ */

if (isset($conn) && $conn instanceof mysqli) {

    $conn->close();

}

?>


<?php include '../includes/header.php'; ?>

<?php include '../includes/navigation.php'; ?>


<main class="login-page">


    <div class="login-box">


        <h1>Login</h1>


        <!-- ================================================
             ERROR MESSAGE
             ================================================ -->

        <?php if (!empty($error)) : ?>

            <p class="error">
                <?php echo htmlspecialchars($error); ?>
            </p>

        <?php endif; ?>


        <!-- ================================================
             LOGIN FORM
             ================================================ -->

        <form method="POST" action="">


            <!-- Username / Email -->

            <label for="email">
                Username or Email
            </label>

            <br>


            <input
                type="text"
                id="email"
                name="email"
                value="<?php
                    echo isset($_POST['email'])
                        ? htmlspecialchars($_POST['email'])
                        : '';
                ?>"
                maxlength="100"
                required
            >


            <br><br>


            <!-- Password -->

            <label for="password">
                Password
            </label>

            <br>


            <input
                type="password"
                id="password"
                name="password"
                required
            >


            <br><br>


            <!-- Login Button -->

            <button type="submit">
                Login
            </button>


            <!-- Sign Up -->

            <p class="signup-link">

                Don't have an account?

                <a href="signup.php">
                    Sign Up Now
                </a>

            </p>


        </form>


    </div>


    <script src="../js/script.js"></script>


</main>


<?php include '../includes/footer.php'; ?>