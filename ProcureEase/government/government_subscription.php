<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include('../includes/db_connect.php');

header('Content-Type: application/json');

error_reporting(0); // disable notices/warnings if this is production
ini_set('display_errors', 0);

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'government') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

$government_id = $_SESSION['user_id'];

// Validate required fields
$required_fields = ['full_name', 'contact_number', 'payment_method', 'plan_type', 'plan_amount', 'amount_sent'];
foreach ($required_fields as $field) {
    if (empty($_POST[$field])) {
        echo json_encode(['success' => false, 'message' => "Missing field: $field"]);
        exit();
    }
}

// File upload handling
if (!isset($_FILES['proof_of_payment']) || $_FILES['proof_of_payment']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['success' => false, 'message' => 'Error uploading file']);
    exit();
}

// Upload the image
$upload_dir = '../uploads/payments/';
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}
$filename = uniqid() . '_' . basename($_FILES['proof_of_payment']['name']);
$target_path = $upload_dir . $filename;

if (!move_uploaded_file($_FILES['proof_of_payment']['tmp_name'], $target_path)) {
    echo json_encode(['success' => false, 'message' => 'Failed to move uploaded file']);
    exit();
}

// Compute subscription dates
$start_date = date('Y-m-d');
$end_date = $start_date;

$start_date = date('Y-m-d');
$plan_type = strtolower(trim($_POST['plan_type']));

if (strpos($plan_type, 'monthly') !== false) {
    $end_date = date('Y-m-d', strtotime('+1 month', strtotime($start_date)));
} elseif (strpos($plan_type, 'yearly') !== false) {
    $end_date = date('Y-m-d', strtotime('+1 year', strtotime($start_date)));
} else {
    $end_date = $start_date;
}

// Insert into database
$stmt = $conn->prepare("INSERT INTO subscription_payments 
    (user_id, full_name, contact_number, payment_method, plan_type, plan_amount, amount_sent, proof_image, start_date, end_date, status) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')");

$stmt->bind_param(
    'issssddsss',
    $government_id,
    $_POST['full_name'],
    $_POST['contact_number'],
    $_POST['payment_method'],
    $plan_type,        // use normalized plan_type here
    $_POST['plan_amount'],
    $_POST['amount_sent'],
    $filename,
    $start_date,
    $end_date
);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Payment submitted successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Database insertion failed']);
}

$stmt->close();
$conn->close();
