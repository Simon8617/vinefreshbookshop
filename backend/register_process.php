<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $package_id = $_POST['package_id'];

    $stmt = $conn->prepare("INSERT INTO registrations (fullname, email, phone, package_id) VALUES (?,?,?,?)");
    $stmt->bind_param("sssi", $fullname, $email, $phone, $package_id);

    if ($stmt->execute()) {
        echo "<p style='color:green;'>Registration successful! We’ll contact you soon.</p>";
    } else {
        echo "<p style='color:red;'>Error: " . $conn->error . "</p>";
    }
}
?>
