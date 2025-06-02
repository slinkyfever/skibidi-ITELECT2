<?php
include('../includes/db_connect.php');


if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'government') {
    header('Location: login.php');
    exit();
}

$government_id = $_SESSION['user_id'];

$query = "SELECT o.product_id, o.agency, o.amount, o.status, o.created_at,
                 p.product_name, p.product_description 
          FROM orders o 
          JOIN products p ON o.product_id = p.product_id 
          WHERE o.user_id = ? 
          ORDER BY o.created_at DESC";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $government_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Orders</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="max-w-6xl mx-auto py-10 px-4">
        <h1 class="text-4xl font-bold text-amber-600 mb-8">My Orders</h1>

        <?php if ($result->num_rows > 0): ?>
            <div class="grid gap-6 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-3">
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="bg-white p-6 rounded-2xl shadow hover:shadow-lg transition">
                        <h2 class="text-xl font-semibold text-gray-800 mb-2">
                            <?= htmlspecialchars($row['product_name']) ?>
                        </h2>
                        <p class="text-gray-600 text-sm mb-3">
                            <?= nl2br(htmlspecialchars($row['product_description'])) ?>
                        </p>
                        <p><span class="font-medium">Agency:</span> <?= htmlspecialchars($row['agency']) ?></p>
                        <p><span class="font-medium">Amount:</span> ₱<?= number_format($row['amount'], 2) ?></p>
                        <p class="mt-1">
                            <span class="font-medium">Status:</span>
                            <span class="inline-block px-3 py-1 rounded-full text-sm 
                                <?= $row['status'] === 'Pending Confirmation' 
                                    ? 'bg-yellow-100 text-yellow-700' 
                                    : 'bg-green-100 text-green-700' ?>">
                                <?= htmlspecialchars($row['status']) ?>
                            </span>
                        </p>
                        <p class="text-gray-500 text-xs mt-2">
                            Ordered on: <?= date("F j, Y g:i A", strtotime($row['created_at'])) ?>
                        </p>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <p class="text-gray-600 text-lg">You haven't placed any orders yet.</p>
        <?php endif; ?>
    </div>
</body>
</html>
