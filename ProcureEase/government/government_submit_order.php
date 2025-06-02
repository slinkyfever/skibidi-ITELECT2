<?php
include('../includes/db_connect.php');
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'government') {
    header('Location: login.php');
    exit();
}

// Handle form data
$product_id = $_POST['product_id'];
$full_name = $_POST['full_name'];
$location = $_POST['location'];
$agency = $_POST['agency'];
$payment_method = $_POST['payment_method'];
$gcash_number = $_POST['gcash_number'];
$amount = $_POST['amount'];

// File upload
$proof_image = $_FILES['proof_image']['name'];
$target_dir = "../uploads/proof/";

if (!is_dir($target_dir)) {
    mkdir($target_dir, 0777, true); // create folder if not exists
}

$unique_filename = uniqid() . '-' . basename($proof_image); // avoid overwriting
$target_file = $target_dir . $unique_filename;

if (move_uploaded_file($_FILES["proof_image"]["tmp_name"], $target_file)) {
    // File moved successfully, continue with DB insert

   $query = "INSERT INTO orders (
    product_id, user_id, full_name, location, agency, payment_method, gcash_number, amount, proof_of_payment, status, created_at
) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', NOW())";

$stmt = $conn->prepare($query);
$stmt->bind_param(
    'iissssdss',
    $product_id,
    $_SESSION['user_id'],
    $full_name,
    $location,
    $agency,
    $payment_method,
    $gcash_number,
    $amount,
    $unique_filename
);
    if ($stmt->execute()) {
        echo "<script>alert('Order submitted successfully!'); window.location.href = 'government_dashboard.php?';</script>";
    } else {
        echo "Database Error: " . $stmt->error;
    }

} else {
    echo "Error: Failed to upload proof of payment image.";
}
?>
