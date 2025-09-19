<?php
// order_actions.php
include 'db.php';
header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);
$action = $data['action'] ?? '';

if ($action === 'update') {
    $id = intval($data['id']);
    $name = trim($data['customer_name']);
    $phone = trim($data['customer_phone']);
    $address = trim($data['customer_address']);
    $quantity = intval($data['quantity']);
    $status = trim($data['status']);

    if ($id <= 0 || empty($name) || empty($phone) || empty($address) || $quantity <= 0) {
        echo json_encode(["status" => "error", "message" => "⚠️ Invalid input data."]);
        exit;
    }

    $stmt = $conn->prepare("UPDATE orders SET customer_name=?, customer_phone=?, customer_address=?, quantity=?, status=? WHERE id=?");
    $stmt->bind_param("sssisi", $name, $phone, $address, $quantity, $status, $id);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "✅ Order updated successfully."]);
    } else {
        echo json_encode(["status" => "error", "message" => "❌ Failed to update order."]);
    }
    exit;
}

if ($action === 'delete') {
    $id = intval($data['id']);
    if ($id <= 0) {
        echo json_encode(["status" => "error", "message" => "⚠️ Invalid order ID."]);
        exit;
    }
    $stmt = $conn->prepare("DELETE FROM orders WHERE id=?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "🗑 Order deleted successfully."]);
    } else {
        echo json_encode(["status" => "error", "message" => "❌ Failed to delete order."]);
    }
    exit;
}

echo json_encode(["status" => "error", "message" => "⚠️ Invalid action."]);
