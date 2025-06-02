<?php
include('../includes/db_connect.php');
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'supplier') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

if (!isset($_GET['order_id'])) {
    echo json_encode(['success' => false, 'message' => 'Order ID is required']);
    exit();
}

$order_id = intval($_GET['order_id']);

$query = "SELECT o.*, p.product_name FROM orders o
          LEFT JOIN products p ON o.product_id = p.product_id
          WHERE o.order_id = ? LIMIT 1";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Order not found']);
    exit();
}

$order = $result->fetch_assoc();

echo json_encode(['success' => true, 'order' => $order]);
?>
