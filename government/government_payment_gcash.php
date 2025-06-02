<?php
// government_payment_gcash.php
session_start();
include('../includes/db_connect.php');

// Check government user login
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'government') {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Insert order from POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'], $_POST['amount'], $_POST['name'], $_POST['location'], $_POST['agency'], $_POST['gcash_number'])) {
    $product_id = intval($_POST['product_id']);
    $amount = floatval($_POST['amount']);
    $name = $_POST['name'];
    $location = $_POST['location'];
    $agency = $_POST['agency'];
    $gcash_number = $_POST['gcash_number'];

    $stmt = $conn->prepare("INSERT INTO orders (user_id, product_id, amount, name, location, agency, gcash_number, status) VALUES (?, ?, ?, ?, ?, ?, ?, 'pending')");
    $stmt->bind_param('iidssss', $user_id, $product_id, $amount, $name, $location, $agency, $gcash_number);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        $order_id = $stmt->insert_id;
        $stmt->close();
        header("Location: government_payment_gcash.php?order_id=$order_id");
        exit();
    } else {
        die("Failed to create order.");
    }
}

// Show payment & upload form if order_id present
if (isset($_GET['order_id'])) {
    $order_id = intval($_GET['order_id']);
    $stmt = $conn->prepare("SELECT * FROM orders WHERE order_id = ? AND user_id = ?");
    $stmt->bind_param('ii', $order_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 0) {
        die("Order not found.");
    }
    $order = $result->fetch_assoc();
    $stmt->close();

    $uploadSuccess = false;
    $uploadError = '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['proof_of_payment'])) {
        $uploadDir = '../uploads/proof_of_payments/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

        $file = $_FILES['proof_of_payment'];
        $filename = basename($file['name']);
        $targetFile = $uploadDir . time() . '_' . preg_replace('/[^a-zA-Z0-9_.-]/', '_', $filename);

        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        if (!in_array($ext, $allowed)) {
            $uploadError = "Invalid file type. Please upload an image.";
        } elseif ($file['error'] !== 0) {
            $uploadError = "Error uploading file.";
        } else {
            if (move_uploaded_file($file['tmp_name'], $targetFile)) {
                $proof_path = 'uploads/proof_of_payments/' . basename($targetFile);
                $stmt = $conn->prepare("UPDATE orders SET proof_of_payment = ?, status = 'paid' WHERE order_id = ?");
                $stmt->bind_param('si', $proof_path, $order_id);
                $stmt->execute();
                $stmt->close();
                $uploadSuccess = true;
            } else {
                $uploadError = "Failed to move uploaded file.";
            }
        }
    }

    ?>

    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8" />
        <title>GCash Payment</title>
        <script src="https://cdn.tailwindcss.com"></script>
        <style>
            #message {
                opacity: 1;
                transition: opacity 1s ease-out;
                margin-top: 10px;
                text-align: center;
            }
        </style>
        <?php if ($uploadSuccess): ?>
        <script>
            window.onload = function() {
                setTimeout(() => {
                    const msg = document.getElementById('message');
                    if(msg) msg.style.opacity = 0;
                }, 4000);
                setTimeout(() => {
                    window.location.href = 'government_dashboard.php';
                }, 5000);
            }
        </script>
        <?php endif; ?>
    </head>
    <body class="bg-gray-100 py-10 px-6">
        <div class="max-w-xl mx-auto bg-white p-6 rounded shadow-lg">
            <h2 class="text-2xl font-bold mb-4">Pay ₱<?= number_format($order['amount'], 2) ?> with GCash</h2>
            <p class="mb-4">Scan the QR code below using your GCash app to make the payment.</p>

            <img src="../assets/gcash_qr.png" alt="GCash QR Code" class="mx-auto mb-6 w-48 h-48 object-contain" />

            <?php if ($uploadSuccess): ?>
                <div id="message" class="text-green-600 font-semibold">Proof of payment uploaded successfully! Redirecting...</div>
            <?php else: ?>
                <?php if ($uploadError): ?>
                    <div id="message" class="text-red-600 font-semibold"><?= htmlspecialchars($uploadError) ?></div>
                <?php endif; ?>
                <h3 class="text-lg font-semibold mb-2">Upload Proof of Payment</h3>
                <form action="government_payment_gcash.php?order_id=<?= $order_id ?>" method="POST" enctype="multipart/form-data" class="space-y-4">
                    <input type="file" name="proof_of_payment" required accept="image/*" />
                    <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded hover:bg-green-700">Upload Proof</button>
                </form>
                <p class="mt-6 text-sm text-gray-600">After uploading, your payment will be verified.</p>
            <?php endif; ?>
        </div>
    </body>
    </html>

    <?php
    exit();
}

// No POST and no order_id
die("No order found. Please place an order first.");
