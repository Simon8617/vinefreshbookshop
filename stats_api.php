<?php
include 'db.php';

$stats = [
    "feedback" => 0,
    "subscribers" => 0,
    "packages" => 0,
    "students" => 0
];

$res = $conn->query("SELECT COUNT(*) as c FROM feedback"); 
$stats['feedback'] = $res->fetch_assoc()['c'];

$res = $conn->query("SELECT COUNT(*) as c FROM subscribers"); 
$stats['subscribers'] = $res->fetch_assoc()['c'];

$res = $conn->query("SELECT COUNT(*) as c FROM packages"); 
$stats['packages'] = $res->fetch_assoc()['c'];

$res = $conn->query("SELECT COUNT(*) as c FROM registrations"); 
$stats['students'] = $res->fetch_assoc()['c'];

echo json_encode($stats);
?>
