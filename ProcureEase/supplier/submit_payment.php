<?php
include('../includes/db_connect.php');
session_start();
header('Content-Type: application/json');

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in.']);
    exit;
}

$user_id = $_SESSION['user_id'];

// Handle file upload
$proofPath = '';
if (isset($_FILES['proof']) && $_FILES['proof']['error'] === 0) {
    $uploadDir = 'uploads/';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
    $filename = uniqid() . '_' . basename($_FILES['proof']['name']);
    $proofPath = $uploadDir . $filename;

    if (!move_uploaded_file($_FILES['proof']['tmp_name'], $proofPath)) {
        echo json_encode(['success' => false, 'message' => 'Failed to upload proof of payment.']);
        exit;
    }
}

// Get and sanitize data
$plan = $conn->real_escape_string($_POST['plan']);
$amount = (float) $_POST['amount'];
$name = $conn->real_escape_string($_POST['name']);
$contact = $conn->real_escape_string($_POST['contact']);
$payment_method = $conn->real_escape_string($_POST['payment_method']);
$amount_sent = (float) $_POST['amount_sent'];
$start_date = date('Y-m-d');

if (stripos($plan, 'yearly') !== false) {
    $end_date = date('Y-m-d', strtotime('+1 year'));
} else {
    $end_date = date('Y-m-d', strtotime('+1 month'));
}
$status = 'pending';

// Prepare SQL statement
$stmt = $conn->prepare("INSERT INTO subscription_payments 
    (user_id, full_name, contact_number, payment_method, plan_type, plan_amount, amount_sent, proof_image, start_date, end_date, status) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Prepare failed: ' . $conn->error]);
    exit;
}

// Correct bind types: i = int, s = string, d = double
$stmt->bind_param("isssssdssss", 
    $user_id, $name, $contact, $payment_method, $plan, $amount, $amount_sent, $proofPath, $start_date, $end_date, $status
);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Payment submitted successfully.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Execution failed: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
?>
