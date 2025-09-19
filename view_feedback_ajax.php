<?php
include('db.php');

$search = isset($_GET['search']) ? "%" . $_GET['search'] . "%" : "%";

// Search by fullname, email, or message
$stmt = $conn->prepare("SELECT * FROM feedback 
                        WHERE fullname LIKE ? OR email LIKE ? OR message LIKE ? 
                        ORDER BY submitted_at DESC");
$stmt->bind_param("sss", $search, $search, $search);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo "<table>
            <tr>
              <th>ID</th>
              <th>Full Name</th>
              <th>Email</th>
              <th>Message</th>
              <th>Date</th>
              <th>Action</th>
            </tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>{$row['id']}</td>
                <td>".htmlspecialchars($row['fullname'])."</td>
                <td>".htmlspecialchars($row['email'])."</td>
                <td>".nl2br(htmlspecialchars($row['message']))."</td>
                <td>{$row['submitted_at']}</td>
                <td>
                  <button class='reply-btn' onclick=\"openReply('{$row['email']}')\">Reply</button>
                  <button class='delete-btn' onclick='deleteFeedback({$row['id']})'>Delete</button>
                </td>
              </tr>";
    }
    echo "</table>";
} else {
    echo "<p style='text-align:center; color:gray;'>No feedback found.</p>";
}
?>
