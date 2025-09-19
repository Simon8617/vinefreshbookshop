<?php
session_start();
include('db.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_feedback'])) {
    $fullname = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    $message = trim($_POST['message']);

    if (!empty($fullname) && !empty($email) && !empty($message)) {
        $stmt = $conn->prepare("INSERT INTO feedback (fullname, email, message) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $fullname, $email, $message);

        if ($stmt->execute()) {
            $_SESSION['feedback_status'] = "✅ Thank you, your feedback has been sent successfully!";
        } else {
            $_SESSION['feedback_status'] = "❌ Error sending feedback: " . $conn->error;
        }
    } else {
        $_SESSION['feedback_status'] = "⚠️ Please fill in all fields.";
    }

    header("Location: services.php");
    exit;
} else {
    header("Location: services.php");
    exit;
}
?>
