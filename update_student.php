<?php
// update_student.php
include 'db.php';

if (isset($_POST['id'])) {
    $id = intval($_POST['id']);
    $firstname = trim($_POST['firstname']);
    $middlename = trim($_POST['middlename']);
    $lastname = trim($_POST['lastname']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $pack1 = trim($_POST['pack1']);
    $pack2 = trim($_POST['pack2']);
    $pack3 = trim($_POST['pack3']);
    $pack4 = trim($_POST['pack4']);
    $pack5 = trim($_POST['pack5']);
    $pack6 = trim($_POST['pack6']);
    $pack7 = trim($_POST['pack7']);
    $pack8 = trim($_POST['pack8']);
    $pack9 = trim($_POST['pack9']);

    $stmt = $conn->prepare("UPDATE registrations 
        SET firstname=?, middlename=?, lastname=?, email=?, phone=?, 
            pack1=?, pack2=?, pack3=?, pack4=?, pack5=?, pack6=?, pack7=?, pack8=?, pack9=? 
        WHERE id=?");

    if (!$stmt) {
        echo "❌ Prepare failed: " . $conn->error;
        exit;
    }

    $stmt->bind_param("ssssssssssssssi", 
        $firstname, $middlename, $lastname, $email, $phone,
        $pack1, $pack2, $pack3, $pack4, $pack5, $pack6, $pack7, $pack8, $pack9,
        $id
    );

    echo $stmt->execute() ? "success" : "error: " . $stmt->error;
}
?>
