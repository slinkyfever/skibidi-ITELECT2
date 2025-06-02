<?php
include('../includes/db_connect.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = intval($_POST['user_id']);
    $action = $_POST['action'];

    if ($action === 'approve') {
        $conn->query("UPDATE users SET is_new = 0 WHERE id = $userId");
        header("Location: admin_dashboard.php?status=approved");
        exit();
    } elseif ($action === 'reject') {
        $conn->query("DELETE FROM users WHERE id = $userId");
        header("Location: admin_dashboard.php?status=rejected");
        exit();
    } else {
        header("Location: admin_dashboard.php?status=invalid_action");
        exit();
    }
}
?>

