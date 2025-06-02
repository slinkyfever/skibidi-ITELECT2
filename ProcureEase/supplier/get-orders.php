<?php
header('Content-Type: application/json');

include('../includes/db_connect.php');
session_start();

// Check connection
if (!$conn) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

// Query to get orders joined with products to get product name
$sql = "
    SELECT o.order_id, o.full_name, p.product_name, o.amount, o.location, o.payment_method, o.status
    FROM orders o
    LEFT JOIN products p ON o.product_id = p.product_id
    ORDER BY o.created_at DESC
";

$result = mysqli_query($conn, $sql);

if ($result) {
    $orders = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $orders[] = $row;
    }
    echo json_encode(['success' => true, 'orders' => $orders]);
} else {
    echo json_encode(['success' => false, 'message' => 'Query failed']);
}

mysqli_close($conn);
