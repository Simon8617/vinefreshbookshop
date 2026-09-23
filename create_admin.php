<?php
include("db.php");

$username = "admin";
$password = "admin123"; // plain text password

// Hash the password
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Insert into DB
$stmt = $conn->prepare("INSERT INTO admins (username, password) VALUES (?, ?)");
$stmt->bind_param("ss", $username, $hashed_password);

if ($stmt->execute()) {
    echo "✅ Admin account created successfully!<br>";
    echo "👉 Username: $username<br>";
    echo "👉 Password: $password<br>";
} else {
    echo "❌ Error: " . $conn->error;
}
?>
