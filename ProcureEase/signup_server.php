<?php
include('./includes/db_connect.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? '';

    // Basic validation
    if (empty($name) || empty($email) || empty($password) || $role === 'invalid') {
        echo "error:All fields are required.";
        exit;
    }

    // Check for existing email
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    if (!$stmt) {
        echo "error:Database error (SELECT): " . $conn->error;
        exit;
    }

    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "error:Email already exists.";
        exit;
    }

    // Insert new user
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
    if (!$stmt) {
        echo "error:Database error (INSERT): " . $conn->error;
        exit;
    }

    $stmt->bind_param("ssss", $name, $email, $hashedPassword, $role);
    if ($stmt->execute()) {
        echo "redirect:login.php";
    } else {
        echo "error:Failed to create account. " . $stmt->error;
    }
}
?>
