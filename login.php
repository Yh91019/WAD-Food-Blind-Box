<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Blind Bite | Login</title>
    <link rel="stylesheet" href="login.css">
</head>

<body>

<div class="container">

    <div class="left">
        <a href="index.php"><img src="shop logo.png" alt="Blind Bite Logo"></a>

        <h1>Welcome to Blind Bite</h1>

        <p>
            Discover surprise meals, save money, and help reduce food waste.
        </p>
    </div>

    <div class="right">

        <!-- Login Form -->
        <div id="loginForm">

            <h2>Login</h2>

            <form action="login_process.php" method="post">

                <input type="email"
                       name="email"
                       placeholder="Email Address"
                       required>

                <input type="password"
                       name="password"
                       placeholder="Password"
                       required>

                <button type="submit">
                    Login
                </button>

            </form>

            <p>
                Don't have an account?
                <a href="#" onclick="showRegister()">Register</a>
            </p>

        </div>

        <!-- Register Form -->
        <div id="registerForm" style="display:none;">

            <h2>Create Account</h2>

            <form action="register_process.php" method="post">

                <input type="text"
                       name="fullname"
                       placeholder="Full Name"
                       required>

                <input type="email"
                       name="email"
                       placeholder="Email Address"
                       required>

                <input type="password"
                       name="password"
                       placeholder="Password"
                       required>

                <input type="password"
                       name="confirmPassword"
                       placeholder="Confirm Password"
                       required>

                <button type="submit">
                    Register
                </button>

            </form>

            <p>
                Already have an account?
                <a href="#" onclick="showLogin()">Login</a>
            </p>

        </div>

    </div>

</div>

<script>

function showRegister(){
    document.getElementById("loginForm").style.display="none";
    document.getElementById("registerForm").style.display="block";
}

function showLogin(){
    document.getElementById("registerForm").style.display="none";
    document.getElementById("loginForm").style.display="block";
}

</script>

</body>

</html>