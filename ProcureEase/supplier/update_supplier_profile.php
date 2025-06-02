<?php
include('../includes/db_connect.php');
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    $company_name = $_POST['company_name'] ?? '';
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $contact = $_POST['contact'] ?? '';

    // Handle profile image upload
    $profile_image = null;
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
        $ext = pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION);
        $targetDir = "../uploads/";
        $filename = uniqid() . '.' . $ext;
        $targetFile = $targetDir . $filename;

        if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $targetFile)) {
            $profile_image = $targetFile;
        }
    }

    // Update `users` table
    $stmt = $conn->prepare("UPDATE users SET name = ?, email = ? WHERE id = ?");
    $stmt->bind_param("ssi", $name, $email, $user_id);
    $stmt->execute();

    // Update `suppliers` table
    if ($profile_image) {
        $stmt = $conn->prepare("UPDATE suppliers SET company_name = ?, contact = ?, profile_image = ? WHERE user_id = ?");
        $stmt->bind_param("sssi", $company_name, $contact, $profile_image, $user_id);
    } else {
        $stmt = $conn->prepare("UPDATE suppliers SET company_name = ?, contact = ? WHERE user_id = ?");
        $stmt->bind_param("ssi", $company_name, $contact, $user_id);
    }
    $stmt->execute();

    header('Location: supplier_dashboard.php'); // or your dashboard filename
    exit();
} else {
    header('Location: login.php');
    exit();
}
