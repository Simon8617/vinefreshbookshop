<?php
include('db.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>View Feedback</title>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f8f9fa;
      margin: 20px;
    }
    h2 {
      text-align: center;
      margin-bottom: 20px;
      color: #333;
    }
    .feedback-container {
      max-width: 900px;
      margin: auto;
      background: #fff;
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 20px;
    }
    th, td {
      border: 1px solid #ddd;
      padding: 12px;
      text-align: left;
    }
    th {
      background: #0077cc;
      color: white;
    }
    tr:nth-child(even) {
      background: #f2f2f2;
    }
    button {
      padding: 6px 12px;
      background: #0077cc;
      color: white;
      border: none;
      border-radius: 4px;
      cursor: pointer;
    }
    button:hover {
      background: #005fa3;
    }
    .reply-box {
      display: none;
      margin-top: 10px;
    }
    textarea {
      width: 100%;
      padding: 10px;
      border-radius: 5px;
      border: 1px solid #ccc;
      resize: vertical;
    }
    .replies {
      margin-top: 10px;
      padding: 10px;
      background: #f1f1f1;
      border-radius: 5px;
    }
    .replies p {
      margin: 5px 0;
      font-size: 14px;
    }
  </style>
</head>
<body>

<div class="feedback-container">
  <h2>Customer Feedback</h2>
  <table>
    <tr>
      <th>ID</th>
      <th>Name</th>
      <th>Email</th>
      <th>Message</th>
      <th>Date</th>
      <th>Action</th>
    </tr>

    <?php
    $sql = "SELECT * FROM feedback ORDER BY submitted_at DESC";
    $result = $conn->query($sql);

    if (!$result) {
        echo "<tr><td colspan='6' style='color:red; text-align:center;'>SQL Error: " . $conn->error . "</td></tr>";
    } elseif ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>{$row['id']}</td>
                    <td>" . (isset($row['fullname']) ? $row['fullname'] : $row['name']) . "</td>
                    <td>{$row['email']}</td>
                    <td>{$row['message']}</td>
                    <td>{$row['submitted_at']}</td>
                    <td>
                        <button onclick='toggleReply({$row['id']})'>Reply</button>
                    </td>
                  </tr>
                  <tr>
                    <td colspan='6'>
                      <div id='reply-box-{$row['id']}' class='reply-box'>
                        <textarea id='reply-message-{$row['id']}' placeholder='Write your reply...'></textarea><br>
                        <button onclick='sendReply({$row['id']})'>Send Reply</button>
                        <div id='replies-{$row['id']}' class='replies'></div>
                      </div>
                    </td>
                  </tr>";
        }
    } else {
        echo "<tr><td colspan='6' style='text-align:center;'>No feedback available</td></tr>";
    }
    ?>
  </table>
</div>

<script>
function toggleReply(feedbackId) {
  $("#reply-box-" + feedbackId).toggle();

  // Load existing replies
  $.ajax({
    url: "load_replies.php",
    type: "POST",
    data: { feedback_id: feedbackId },
    success: function(data) {
      $("#replies-" + feedbackId).html(data);
    }
  });
}

function sendReply(feedbackId) {
  var message = $("#reply-message-" + feedbackId).val().trim();
  if (message === "") {
    alert("Reply message cannot be empty!");
    return;
  }

  $.ajax({
    url: "reply_feedback.php",
    type: "POST",
    data: { feedback_id: feedbackId, reply_message: message },
    success: function(response) {
      $("#reply-message-" + feedbackId).val(""); // Clear textarea
      toggleReply(feedbackId); // Reload replies
    }
  });
}
</script>

</body>
</html>
