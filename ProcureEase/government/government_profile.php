<?php
include('../includes/db_connect.php');

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'government') {
    header('Location: login.php');
    exit();
}

$government_id = $_SESSION['user_id'];

$query = "SELECT agency_name, address, contact FROM governments WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $government_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    // No user found - redirect or show error
    echo "User not found.";
    exit();
}

$user = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Government Profile</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-6">
    <div class="max-w-xl mx-auto bg-white rounded-lg shadow-md p-6">
        <h1 class="text-3xl font-bold mb-6 text-amber-600">My Profile</h1>

        <div class="mb-4">
            <label class="block text-gray-700 font-semibold mb-1">Agency Name:</label>
            <p class="text-gray-900"><?= htmlspecialchars($user['agency_name']) ?></p>
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 font-semibold mb-1">Address:</label>
            <p class="text-gray-900"><?= htmlspecialchars($user['address']) ?></p>
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 font-semibold mb-1">Contact Number:</label>
            <p class="text-gray-900"><?= htmlspecialchars($user['contact']) ?></p>
        </div>
    </div>
</body>
</html>
