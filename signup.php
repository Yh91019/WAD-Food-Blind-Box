<?php
session_start();
include 'config/db_connect.php';

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = trim($_POST['username']);
    $password = password_hash($_POST['password'],PASSWORD_DEFAULT);
    $email = trim($_POST['email']);
    $gender = $_POST['gender'];
    $dob = $_POST['date_of_birth'];
    $address = trim($_POST['address']);
    $phone = trim($_POST['phone_number']);

    $check = $conn->prepare("SELECT * FROM users WHERE username=? OR email=?");
    $check->bind_param("ss",$username,$email);
    $check->execute();

    if($check->get_result()->num_rows > 0){

        $error = "Username or Email already exists.";

    }else{

        $sql = "INSERT INTO users
        (username,password,email,gender,date_of_birth,address,phone_number)
        VALUES (?,?,?,?,?,?,?)";

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

        if($stmt->execute()){

            $success = "Registration Successful!";

        }else{

            $error = "Registration failed.";

        }
    }
}
?>

<?php include 'includes/header.php'; ?>
<?php include 'includes/navigation.php'; ?>

<main class="signup-page">

<div class="signup-box">

<h1>Create Account</h1>

<?php
if($error!=""){
    echo "<p class='error'>$error</p>";
}

if($success!=""){
    echo "<p class='success'>$success</p>";
}
?>

<form method="POST">

<label>Username</label><br>
<input type="text" name="username" required>

<br><br>

<label>Email</label><br>
<input type="email" name="email" required>

<br><br>

<label>Password</label><br>
<input type="password" name="password" required>

<br><br>

<label>Gender</label><br>

<select name="gender" required>
    <option value="">Select Gender</option>
    <option value="MALE">Male</option>
    <option value="FEMALE">Female</option>
    <option value="OTHER">Other</option>
</select>

<br><br>

<label>Date of Birth</label><br>
<input type="date" name="date_of_birth" required>

<br><br>

<label>Address</label><br>
<textarea name="address" rows="4" required></textarea>

<br><br>

<label>Phone Number</label><br>
<input type="text" name="phone_number" required>

<br><br>

<button type="submit">
Sign Up
</button>

</form>

</div>

</main>

<?php include 'includes/footer.php'; ?>