<?php
$host = "localhost";     // database host
$user = "root";          // database username (default is "root" in XAMPP)
$pass = "";              // database password (empty in XAMPP by default)
$dbname = "vinefresh";   // database name you created

// Create connection
$conn = new mysqli($host, $user, $pass, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
