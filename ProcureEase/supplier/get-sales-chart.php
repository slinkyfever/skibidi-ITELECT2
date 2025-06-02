<?php
include('../includes/db_connect.php');
header('Content-Type: application/json');
session_start();

$user_id = $_SESSION['user_id'] ?? null;

if (!$user_id) {
    echo json_encode(['error' => 'User not logged in']);
    exit;
}

$product_id = 2;


$query = "
    SELECT 
        MONTH(created_at) AS month,
        SUM(amount) AS total_sales
    FROM orders
    WHERE product_id = ?
      AND status = 'accepted'
      AND YEAR(created_at) = YEAR(CURDATE())
    GROUP BY MONTH(created_at)
    ORDER BY MONTH(created_at)
";

$stmt = $conn->prepare($query);
$stmt->bind_param('i', $product_id);
$stmt->execute();
$result = $stmt->get_result();

$sales = array_fill(0, 12, 0);

while ($row = $result->fetch_assoc()) {
    $month = (int)$row['month'];
    $sales[$month - 1] = (float)$row['total_sales'];
}

$stmt->close();
$conn->close();

echo json_encode([
    'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
    'data' => $sales
]);
