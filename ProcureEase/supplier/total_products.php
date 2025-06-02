<?php
session_start();
header('Content-Type: application/json');

include('../includes/db_connect.php');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'User not logged in']);
    exit;
}

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT COUNT(*) AS total FROM products WHERE user_id = ?");
if (!$stmt) {
    echo json_encode(['error' => 'Prepare failed']);
    exit;
}

$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    echo json_encode(['total' => (int)$row['total']]);
} else {
    echo json_encode(['error' => 'No result']);
}
?>
