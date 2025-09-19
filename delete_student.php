<?php
// delete_student.php
include 'db.php';

if (isset($_POST['id'])) {
    $id = intval($_POST['id']);

    // Prepare delete query
    $stmt = $conn->prepare("DELETE FROM registrations WHERE id=?");
    if (!$stmt) {
        echo "error"; // SQL prepare failed
        exit;
    }

    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "error";
    }

    $stmt->close();
}
$conn->close();
?>
