<?php
include('../includes/db_connect.php');

// Only start session if one hasn't been started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is admin
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

// Handle subscription status updates
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['subscription_id']) && isset($_POST['action'])) {
        $subscription_id = intval($_POST['subscription_id']);
        $action = $_POST['action'];
        
        if ($action === 'approve' || $action === 'reject') {
            $status = ($action === 'approve') ? 'approved' : 'rejected';
            
            // Start transaction
            $conn->begin_transaction();
            
            try {
                // Update subscription status
                $update_query = "UPDATE subscription_payments SET status = ? WHERE id = ?";
                $stmt = $conn->prepare($update_query);
                $stmt->bind_param("si", $status, $subscription_id);
                $stmt->execute();
                
                if ($action === 'approve') {
                    // Get the user_id from the subscription
                    $user_query = "SELECT user_id FROM subscription_payments WHERE id = ?";
                    $stmt = $conn->prepare($user_query);
                    $stmt->bind_param("i", $subscription_id);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $user_data = $result->fetch_assoc();
                    
                    if ($user_data) {
                        // Update user's subscription status in users table if needed
                        $update_user = "UPDATE users SET has_active_subscription = 1 WHERE id = ?";
                        $stmt = $conn->prepare($update_user);
                        $stmt->bind_param("i", $user_data['user_id']);
                        $stmt->execute();
                    }
                }
                
                // Commit transaction
                $conn->commit();
                
                $_SESSION['message'] = "Subscription has been " . $status;
                $_SESSION['message_type'] = "success";
            } catch (Exception $e) {
                // Rollback transaction on error
                $conn->rollback();
                $_SESSION['message'] = "Error updating subscription status: " . $e->getMessage();
                $_SESSION['message_type'] = "error";
            }
            
            $stmt->close();
        }
    }
}

// Fetch subscription payments with user and government details
// wew hahaha
$query = "SELECT sp.*, u.email, g.agency_name 
          FROM subscription_payments sp 
          JOIN users u ON sp.user_id = u.id 
          LEFT JOIN governments g ON u.id = g.user_id 
          ORDER BY sp.created_at DESC";

$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Subscription Review</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <h2 class="text-3xl font-bold text-amber-600 mb-6">Subscription Review</h2>

        <?php if (isset($_SESSION['message'])): ?>
            <div class="mb-4 p-4 rounded <?= $_SESSION['message_type'] === 'success' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' ?>">
                <?= $_SESSION['message'] ?>
            </div>
            <?php unset($_SESSION['message'], $_SESSION['message_type']); ?>
        <?php endif; ?>

        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User Details</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subscription Details</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Payment Details</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900"><?= htmlspecialchars($row['full_name']) ?></div>
                                <div class="text-sm text-gray-500"><?= htmlspecialchars($row['email']) ?></div>
                                <div class="text-sm text-gray-500"><?= htmlspecialchars($row['agency_name'] ?? 'N/A') ?></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900"><?= htmlspecialchars($row['plan_type']) ?></div>
                                <div class="text-sm text-gray-500">₱<?= number_format($row['plan_amount'], 2) ?></div>
                                <div class="text-sm text-gray-500">
                                    <?= date('M d, Y', strtotime($row['start_date'])) ?> - 
                                    <?= date('M d, Y', strtotime($row['end_date'])) ?>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900"><?= htmlspecialchars($row['payment_method']) ?></div>
                                <div class="text-sm text-gray-500">Amount Sent: ₱<?= number_format($row['amount_sent'], 2) ?></div>
                                <?php if ($row['proof_image']): ?>
                                    <a href="../uploads/payments/<?= htmlspecialchars($row['proof_image']) ?>" 
                                       target="_blank" 
                                       class="text-blue-600 hover:text-blue-800 text-sm">
                                        View Proof
                                    </a>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    <?= $row['status'] === 'approved' ? 'bg-green-100 text-green-800' : 
                                        ($row['status'] === 'rejected' ? 'bg-red-100 text-red-800' : 
                                        'bg-yellow-100 text-yellow-800') ?>">
                                    <?= ucfirst($row['status']) ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <?php if ($row['status'] === 'pending'): ?>
                                    <form method="POST" class="inline-block">
                                        <input type="hidden" name="subscription_id" value="<?= $row['id'] ?>">
                                        <button type="submit" name="action" value="approve" 
                                                class="text-green-600 hover:text-green-900 mr-3">
                                            Approve
                                        </button>
                                        <button type="submit" name="action" value="reject" 
                                                class="text-red-600 hover:text-red-900">
                                            Reject
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <span class="text-gray-500">No actions available</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>


