<?php
include('../includes/db_connect.php');
header('Content-Type: application/json');

$sql = "SELECT COUNT(*) as total FROM orders WHERE status = 'pending'";
$result = $conn->query($sql);
$data = $result->fetch_assoc();

echo json_encode(['total' => $data['total']]);
