<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email'], $_POST['message'])) {
    $to = $_POST['email'];
    $subject = "Reply from Admin";
    $message = nl2br(htmlspecialchars($_POST['message']));
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "From: noreply@yourdomain.com\r\n";

    if (mail($to, $subject, $message, $headers)) {
        echo "✅ Reply sent successfully!";
    } else {
        echo "❌ Failed to send reply. Check mail server settings.";
    }
}
?>
