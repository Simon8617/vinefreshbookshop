<?php
include('db.php');

// ✅ Single delete
if (isset($_POST['subscriber_id'])) {
    $id = intval($_POST['subscriber_id']);

    $stmt = $conn->prepare("DELETE FROM subscribers WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "Error: " . $conn->error;
    }
    $stmt->close();
}

// ✅ Bulk delete
if (isset($_POST['bulk_ids'])) {
    $ids = $_POST['bulk_ids'];
    $ids_placeholder = implode(',', array_fill(0, count($ids), '?'));
    $types = str_repeat('i', count($ids));

    $stmt = $conn->prepare("DELETE FROM subscribers WHERE id IN ($ids_placeholder)");
    $stmt->bind_param($types, ...$ids);

    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "Error: " . $conn->error;
    }
    $stmt->close();
}
?>
