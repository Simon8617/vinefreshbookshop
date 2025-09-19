<?php
include('db.php');

if (isset($_POST['feedback_id'])) {
    $feedback_id = intval($_POST['feedback_id']);

    $sql = "SELECT * FROM feedback_replies WHERE feedback_id = ? ORDER BY replied_at ASC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $feedback_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<p><strong>Admin:</strong> {$row['reply_message']} <br>
                  <small><em>{$row['replied_at']}</em></small></p>";
        }
    } else {
        echo "<p>No replies yet.</p>";
    }

    $stmt->close();
}
?>
