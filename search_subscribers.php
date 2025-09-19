<?php
include('db.php');

$keyword = isset($_GET['keyword']) ? "%" . $_GET['keyword'] . "%" : "%";

$stmt = $conn->prepare("SELECT * FROM subscribers WHERE email LIKE ? ORDER BY subscribed_at DESC");
$stmt->bind_param("s", $keyword);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<tr id='subscriber-{$row['id']}'>
                <td><input type='checkbox' name='subscriber_ids[]' value='{$row['id']}'></td>
                <td>{$row['id']}</td>
                <td>{$row['email']}</td>
                <td>{$row['subscribed_at']}</td>
                <td><button type='button' class='btn-delete' onclick='deleteSubscriber({$row['id']})'>Delete</button></td>
              </tr>";
    }
} else {
    echo "<tr><td colspan='5' style='text-align:center;'>No matching subscribers found</td></tr>";
}
$stmt->close();
?>
