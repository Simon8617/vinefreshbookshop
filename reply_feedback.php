<?php
include('db.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $feedback_id = intval($_POST['feedback_id']);
    $reply_message = trim($_POST['reply_message']);

    if ($feedback_id > 0 && !empty($reply_message)) {
        $stmt = $conn->prepare("INSERT INTO feedback_replies (feedback_id, reply_message) VALUES (?, ?)");
        $stmt->bind_param("is", $feedback_id, $reply_message);

        if ($stmt->execute()) {
            echo "Reply saved successfully!";
        } else {
            echo "Error: " . $conn->error;
        }

        $stmt->close();
    } else {
        echo "Invalid data!";
    }
} else {
    echo "Invalid request!";
}
?>
