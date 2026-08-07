<?php

// Replace this with the EXACT password you typed during signup
$plainPassword = "0";

// Copy the ENTIRE password hash from your database
$hash = '$2y$12$n4ZWh5FMQdvOMVoSifEkguUdJeaJzYyfPlDsZyIr/HF';

echo "<h2>Password Verification Test</h2>";

echo "<strong>Password Entered:</strong> " . htmlspecialchars($plainPassword) . "<br><br>";

echo "<strong>Hash from Database:</strong><br>";
echo htmlspecialchars($hash) . "<br><br>";

if (password_verify($plainPassword, $hash)) {

    echo "<h3 style='color:green;'>✅ Password Matched!</h3>";

} else {

    echo "<h3 style='color:red;'>❌ Password Did NOT Match!</h3>";

}
?>