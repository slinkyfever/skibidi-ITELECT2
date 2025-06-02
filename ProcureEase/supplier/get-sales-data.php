<?php
session_start();
header('Content-Type: application/json');
include('../includes/db_connect.php');

if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Database connection failed"]);
    exit;
}

$supplierId = isset($_GET['user_id']) ? (int)$_GET['user_id'] : 0;
if ($supplierId === 0) {
    echo json_encode(["success" => false, "message" => "Invalid supplier ID"]);
    exit;
}

// Step 1: Get all product_ids of this supplier
$productSql = "SELECT product_id FROM products WHERE user_id = ?";
$productStmt = $conn->prepare($productSql);
$productStmt->bind_param("i", $supplierId);
$productStmt->execute();
$productResult = $productStmt->get_result();

$productIds = [];
while ($row = $productResult->fetch_assoc()) {
    $productIds[] = $row['product_id'];
}

// If supplier has no products, return empty
if (empty($productIds)) {
    echo json_encode(["success" => true, "sales" => []]);
    exit;
}

// Step 2: Dynamically build query with IN clause
$placeholders = implode(',', array_fill(0, count($productIds), '?'));
$types = str_repeat('i', count($productIds));

$sql = "
    SELECT 
        o.product_id,
        p.product_name,
        COUNT(*) AS quantity_sold,
        SUM(o.amount) AS total_revenue,
        DATE(o.created_at) AS sale_date
    FROM orders o
    JOIN products p ON o.product_id = p.product_id
    WHERE o.product_id IN ($placeholders) AND o.status = 'Accepted'
    GROUP BY o.product_id, sale_date
    ORDER BY sale_date DESC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$productIds);
$stmt->execute();
$result = $stmt->get_result();

$sales = [];
while ($row = $result->fetch_assoc()) {
    $sales[] = [
        "product" => $row["product_name"],
        "quantity" => (int)$row["quantity_sold"],
        "revenue" => number_format((float)$row["total_revenue"], 2),
        "date" => $row["sale_date"]
    ];
}

echo json_encode(["success" => true, "sales" => $sales]);
?>
