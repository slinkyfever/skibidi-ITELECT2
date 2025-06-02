<?php
include('../includes/db_connect.php');
session_start();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_SESSION['user_role'] == 'supplier') {
    $user_id = $_SESSION['user_id'];

    // 1. Check subscription status and validity
    $sub_stmt = $conn->prepare("SELECT status, end_date FROM subscription_payments 
                                WHERE user_id = ? 
                                ORDER BY end_date DESC 
                                LIMIT 1");
    $sub_stmt->bind_param("i", $user_id);
    $sub_stmt->execute();
    $sub_result = $sub_stmt->get_result();
    $subscribed = false;

    if ($sub_result->num_rows > 0) {
        $row = $sub_result->fetch_assoc();
        $today = date('Y-m-d');

        if ($row['status'] === 'approved' && $row['end_date'] >= $today) {
            $subscribed = true;
        }
    }

    // 2. If not subscribed, check posted product count
    if (!$subscribed) {
        $count_stmt = $conn->prepare("SELECT COUNT(*) as product_count FROM products WHERE user_id = ?");
        $count_stmt->bind_param("i", $user_id);
        $count_stmt->execute();
        $count_result = $count_stmt->get_result();
        $product_count = $count_result->fetch_assoc()['product_count'];

        if ($product_count >= 5) {
          echo "limit_reached"; // Keep it simple
exit;
        }
    }

    // 3. Proceed to insert product
    $product_name = $_POST['product_name'];
    $product_description = $_POST['product_description'];
    $product_price = $_POST['product_price'];
    $product_quantity = $_POST['product_quantity'];

    $stmt = $conn->prepare("INSERT INTO products (user_id, product_name, product_description, product_price, product_quantity) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("issdi", $user_id, $product_name, $product_description, $product_price, $product_quantity);

    if ($stmt->execute()) {
        $product_id = $stmt->insert_id;

        $upload_dir = "../uploads/";
        if (!is_dir($upload_dir)) {
            if (!mkdir($upload_dir, 0755, true)) {
                echo "Failed to create uploads directory.";
                exit();
            }
        }

        if (isset($_FILES['product_images'])) {
            foreach ($_FILES['product_images']['tmp_name'] as $key => $tmp_name) {
                $file_name = basename($_FILES['product_images']['name'][$key]);
                $unique_name = time() . '_' . bin2hex(random_bytes(5)) . '_' . $file_name;
                $target_file = $upload_dir . $unique_name;

                if (move_uploaded_file($tmp_name, $target_file)) {
                    $img_stmt = $conn->prepare("INSERT INTO product_images (product_id, image_path) VALUES (?, ?)");
                    $img_stmt->bind_param("is", $product_id, $target_file);
                    $img_stmt->execute();
                }
            }
        }

        echo 'success';
    } else {
        echo 'Failed to post product';
    }
}
?>
