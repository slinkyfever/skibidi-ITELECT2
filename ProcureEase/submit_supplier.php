<?php
include('./includes/db_connect.php');
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_SESSION['user_role'] == 'supplier') {
    $company_name = $_POST['company_name'];
    $address = $_POST['address'];
    $contact = $_POST['contact'];
    $user_id = $_SESSION['user_id'];

    $stmt = $conn->prepare("INSERT INTO suppliers (user_id, company_name, address, contact) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", $user_id, $company_name, $address, $contact);

    if ($stmt->execute()) {
        echo 'submitted'; // Do not update is_new here
    } else {
        echo 'Failed to save data';
    }
}
?>
