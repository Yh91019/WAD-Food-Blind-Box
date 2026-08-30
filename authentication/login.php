<?php

session_start();

include '../config/db_connect.php';

$error = "";
$success = "";

if (isset($_SESSION['registration_message'])) {

    $success = $_SESSION['registration_message'];

    unset($_SESSION['registration_message']);
}



if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $login_id = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';



    if ($login_id === "" && $password === "") {

        $error =
            "Please enter your username/email and password.";

    } elseif ($login_id === "") {

        $error =
            "Please enter your username or email.";

    } elseif ($password === "") {

        $error =
            "Please enter your password.";

    } elseif (strlen($login_id) > 100) {

        $error =
            "Username or email is too long.";

    } else {


 
        $admin_sql = "
            SELECT *
            FROM admin
            WHERE admin_username = ?
        ";

        $admin_stmt =
            $conn->prepare($admin_sql);


        if (!$admin_stmt) {

            die(
                "Database error: " .
                $conn->error
            );

        }


        $admin_stmt->bind_param(
            "s",
            $login_id
        );

        $admin_stmt->execute();

        $admin_result =
            $admin_stmt->get_result();



        if ($admin_result->num_rows == 1) {

            $admin =
                $admin_result->fetch_assoc();

            $stored_admin_password =
                $admin['admin_password'];



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


            if ($admin_password_ok) {

                $_SESSION['username'] =
                    $admin['admin_username'];

                $_SESSION['is_admin'] = true;


                $_SESSION['user_type'] = 'ADMIN';


                $admin_stmt->close();

                $conn->close();


                header(
                    "Location: ../admin/dashboard.php"
                );

                exit();

            }



            else {

                $error =
                    "Incorrect password.";

            }


            $admin_stmt->close();

        }



        else {

            $admin_stmt->close();



            $sql = "
                SELECT *
                FROM users
                WHERE username = ?
                OR email = ?
            ";


            $stmt =
                $conn->prepare($sql);


            if (!$stmt) {

                die(
                    "Database error: " .
                    $conn->error
                );

            }


            $stmt->bind_param(
                "ss",
                $login_id,
                $login_id
            );


            $stmt->execute();

            $result =
                $stmt->get_result();



            if ($result->num_rows == 1) {

                $user =
                    $result->fetch_assoc();


                $stored_password =
                    $user['password'];


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


                if ($password_ok) {


                    $_SESSION['username'] =
                        $user['username'];

                    $_SESSION['email'] =
                        $user['email'];


  
                    $_SESSION['user_type'] =
                        $user['user_type'] ?? 'CUSTOMER';


                    $_SESSION['login_message'] =
                        "Welcome back, " .
                        $user['username'] .
                        "!";


                    $stmt->close();

                    $conn->close();



                    if (
                        $_SESSION['user_type']
                        === 'REVIEWER'
                    ) {

                        header(
                            "Location: ../pages/reviews.php"
                        );

                        exit();

} 
                    header(
                        "Location: ../index.php"
                    );

                    exit();

                }


                else {

                    $error =
                        "Incorrect password.";

                }

            }

            else {

                $error =
                    "Username/email has not been registered.";

            }


            $stmt->close();

        }

    }

}



if (
    isset($conn)
    && $conn instanceof mysqli
) {

    $conn->close();

}

?>


<?php include '../includes/header.php'; ?>

<?php include '../includes/navigation.php'; ?>


<link
    rel="stylesheet"
    href="../css/login.css?v=<?php echo filemtime(__DIR__ . '/../css/login.css'); ?>"
>


<main class="login-page">


    <div class="login-box">


        <h1>Login</h1>



        <?php if (!empty($error)) : ?>

            <p class="error">

                <?php
                echo htmlspecialchars($error);
                ?>

            </p>

        <?php endif; ?>



        <?php if (!empty($success)) : ?>

            <p class="success">

                <?php
                echo htmlspecialchars($success);
                ?>

            </p>

        <?php endif; ?>


        <form
            method="POST"
            action=""
        >

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
                        ? htmlspecialchars(
                            $_POST['email']
                        )
                        : '';

                ?>"
                maxlength="100"
                required
            >


            <br><br>


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


            <button type="submit">

                Login

            </button>


            <p class="signup-link">

                Don't have an account?

                <a href="signup.php">

                    Sign Up Now

                </a>

            </p>


        </form>


    </div>


</main>


<?php include '../includes/footer.php'; ?>