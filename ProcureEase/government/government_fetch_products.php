<?php
include('../includes/db_connect.php');
session_start();

header('Content-Type: application/json');
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Check if government user
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'government') {
    echo json_encode(['error' => 'Unauthorized access']);
    exit();
}

$userId = $_SESSION['user_id'];
$isSubscribed = false;

// Check subscription status
$subQuery = "SELECT status FROM subscription_payments WHERE user_id = ? ORDER BY created_at DESC LIMIT 1";
$subStmt = $conn->prepare($subQuery);
$subStmt->bind_param("i", $userId);
$subStmt->execute();
$subResult = $subStmt->get_result();
if ($subRow = $subResult->fetch_assoc()) {
    if (strtolower($subRow['status']) === 'approved') {
        $isSubscribed = true;
    }
}

// Get filters
$search = $_GET['q'] ?? '';
$min = $_GET['min'] ?? '';
$max = $_GET['max'] ?? '';

$query = "SELECT product_id, product_name, product_description, product_price FROM products";
$conditions = [];
$params = [];
$types = "";

if ($search) {
    $conditions[] = "(product_name LIKE ? OR product_description LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $types .= "ss";
}
if (is_numeric($min)) {
    $conditions[] = "product_price >= ?";
    $params[] = $min;
    $types .= "d";
}
if (is_numeric($max)) {
    $conditions[] = "product_price <= ?";
    $params[] = $max;
    $types .= "d";
}
if ($conditions) {
    $query .= " WHERE " . implode(" AND ", $conditions);
}
$query .= " ORDER BY product_id DESC"; // or RAND() if needed

if (!$isSubscribed) {
    $query .= " LIMIT 5";
}

// Fetch products
$stmt = $conn->prepare($query);
if ($types) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

$products = [];
while ($row = $result->fetch_assoc()) {
    $products[$row['product_id']] = $row;
    $products[$row['product_id']]['images'] = [];
}

// Now fetch images for the listed products
if (!empty($products)) {
    $ids = array_keys($products);
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $types = str_repeat('i', count($ids));

    $imgQuery = "SELECT product_id, image_path FROM product_images WHERE product_id IN ($placeholders)";
    $imgStmt = $conn->prepare($imgQuery);
    $imgStmt->bind_param($types, ...$ids);
    $imgStmt->execute();
    $imgResult = $imgStmt->get_result();

    while ($img = $imgResult->fetch_assoc()) {
        $products[$img['product_id']]['images'][] = $img['image_path'];
    }
}

echo json_encode(array_values($products));
