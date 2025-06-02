<?php
include('../includes/db_connect.php');
session_start();
// Set the correct content type for JSON response
header('Content-Type: application/json');

// Enable error reporting temporarily for debugging (disable later in production)
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Fetch all posted products along with their images
$stmt = $conn->prepare("
    SELECT 
        p.product_id,           
        p.product_name, 
        p.product_description, 
        p.product_price, 
        p.product_quantity,
        pi.image_path 
    FROM 
        products p
    LEFT JOIN 
        product_images pi ON p.product_id = pi.product_id
");

if ($stmt->execute()) {
    $result = $stmt->get_result();
    $products = [];
    
    // Fetch all products and images
    while ($row = $result->fetch_assoc()) {
        $product_id = $row['product_id'];  // Use product_id from the result
        if (!isset($products[$product_id])) {
            // If it's the first time seeing this product, create an entry
            $products[$product_id] = [
                'product_id' => $product_id, 
                'product_name' => $row['product_name'],
                'product_description' => $row['product_description'],
                'product_price' => $row['product_price'],
                'product_quantity' => $row['product_quantity'],
                'images' => []
            ];
        }
        // Add image to the product's images array
        if ($row['image_path']) {
            $products[$product_id]['images'][] = $row['image_path'];
        }
    }

    // Re-index the array for final JSON response
    $products = array_values($products);
    echo json_encode($products);
} else {
    // In case of failure, return a JSON error message
    echo json_encode(['error' => 'Failed to fetch products']);
}
?>
