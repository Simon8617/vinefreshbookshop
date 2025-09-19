<?php
include 'db_connect.php';

$action = $_POST['action'] ?? '';

if($action === 'add'){
    $name = $_POST['name'];
    $stmt = $conn->prepare("INSERT INTO packages (package_name) VALUES (?)");
    $stmt->bind_param("s", $name);
    $stmt->execute();
}
elseif($action === 'edit'){
    $id = $_POST['id'];
    $name = $_POST['name'];
    $stmt = $conn->prepare("UPDATE packages SET package_name=? WHERE id=?");
    $stmt->bind_param("si", $name, $id);
    $stmt->execute();
}
elseif($action === 'delete'){
    $id = $_POST['id'];
    $stmt = $conn->prepare("DELETE FROM packages WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
}
elseif($action === 'bulk_delete'){
    $ids = $_POST['ids'];
    if(is_array($ids)){
        $ids_str = implode(",", array_map("intval", $ids));
        $conn->query("DELETE FROM packages WHERE id IN ($ids_str)");
    }
}
?>
