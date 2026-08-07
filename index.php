<?php
session_start();
?>

<?php include 'includes/header.php'; ?>
<?php include 'includes/navigation.php'; ?>

<?php
if(isset($_SESSION['login_message'])){
?>

<div id="login-popup">
    <?php echo $_SESSION['login_message']; ?>
</div>

<?php
unset($_SESSION['login_message']);
}
?>

<?php
if(isset($_SESSION['logout_message'])){
?>

<div id="logout-popup">
    <?php echo  $_SESSION['logout_message']; ?>
</div>

<?php
unset($_SESSION['logout_message']);
}
?>

<main>
    <img src="../images/bg.jpg" width="100%">
        <article>
            <p>
        <article>
            <p>
			
            </p>
        </article>            </p>
        </article>

</main>
<?php include('includes/footer.php'); ?>