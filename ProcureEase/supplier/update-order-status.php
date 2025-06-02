<?php
include('../includes/db_connect.php');
header('Content-Type: application/json');

$order_id = $_POST['order_id'] ?? null;
$status = $_POST['status'] ?? null;

if (!$order_id || !in_array($status, ['Accepted', 'Declined'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid input']);
    exit;
}

// Use MySQLi instead of PDO
$sql = "UPDATE orders SET status = ? WHERE order_id = ?";
$stmt = $conn->prepare($sql);

if ($stmt) {
    $stmt->bind_param('si', $status, $order_id);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Order not found or status unchanged']);
    }

    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to prepare statement']);
}

$conn->close();
