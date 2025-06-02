<?php
include('../includes/db_connect.php');
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'government') {
    header('Location: login.php');
    exit();
}

if (!isset($_GET['product_id'])) {
    die("No product selected.");
}

$product_id = intval($_GET['product_id']);

// Fetch product details
$query = "SELECT product_id, product_name, product_description, product_price FROM products WHERE product_id = ? LIMIT 1";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $product_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Product not found.");
}

$product = $result->fetch_assoc();

// Fetch product images
$image_query = "SELECT image_path FROM product_images WHERE product_id = ?";
$stmt2 = $conn->prepare($image_query);
$stmt2->bind_param('i', $product_id);
$stmt2->execute();
$image_result = $stmt2->get_result();

$images = [];
while ($row = $image_result->fetch_assoc()) {
    $images[] = $row['image_path'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Order Product</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 py-10 px-6">
<div class="max-w-7xl mx-auto bg-white p-6 rounded shadow-lg">

    <a href="government_dashboard.php?page=products" class="text-blue-600 hover:underline mb-4 inline-block">&lt; Back to Products</a>

    <div class="flex flex-col md:flex-row items-start gap-8">
        <!-- Image Section -->
        <div class="md:w-1/2 w-full">
            <?php if (count($images) > 0): ?>
                <div class="relative w-full h-64 overflow-hidden rounded mb-6">
                    <?php foreach ($images as $index => $img): ?>
                        <img src="../uploads/<?= htmlspecialchars($img) ?>" 
                             class="slider-image absolute top-0 left-0 w-full h-full object-cover transition-opacity duration-700 <?= $index === 0 ? 'opacity-100' : 'opacity-0' ?>"
                             alt="Product Image <?= $index + 1 ?>">
                    <?php endforeach; ?>

                    <!-- Slider Buttons -->
                    <button onclick="prevImage()" type="button" class="absolute top-1/2 left-2 -translate-y-1/2 bg-black bg-opacity-50 text-white px-3 py-1 rounded">‹</button>
                    <button onclick="nextImage()" type="button" class="absolute top-1/2 right-2 -translate-y-1/2 bg-black bg-opacity-50 text-white px-3 py-1 rounded">›</button>
                </div>
            <?php else: ?>
                <p class="text-gray-500 italic">No images available for this product.</p>
            <?php endif; ?>

            <!-- Product details -->
            <div class="text-center">
                <h2 class="text-2xl font-bold mb-2"><?= htmlspecialchars($product['product_name']) ?></h2>
                <p class="text-sm text-gray-600 mb-2"><?= htmlspecialchars($product['product_description']) ?></p>
                <p class="text-lg font-semibold">₱<?= number_format($product['product_price'], 2) ?></p>
            </div>
        </div>

        <!-- Form Section -->
        <div class="md:w-1/2 w-full">
            <h3 class="text-xl font-semibold mb-4">Fill in Your Details</h3>

            <form id="orderForm" action="government_submit_order.php" method="POST" enctype="multipart/form-data" class="space-y-4">
                <input type="hidden" name="product_id" value="<?= $product['product_id'] ?>">
                <input type="hidden" name="amount" value="<?= $product['product_price'] ?>">

                <div>
                    <label class="block text-sm font-medium" for="name">Name</label>
                    <input id="name" type="text" name="full_name" required class="w-full border border-gray-300 rounded px-3 py-2 mt-1" />
                </div>

                <div>
                    <label class="block text-sm font-medium" for="location">Location</label>
                    <input id="location" type="text" name="location" required class="w-full border border-gray-300 rounded px-3 py-2 mt-1" />
                </div>

                <div>
                    <label class="block text-sm font-medium" for="agency">Agency</label>
                    <input id="agency" type="text" name="agency" required class="w-full border border-gray-300 rounded px-3 py-2 mt-1" />
                </div>

                <div>
                    <label class="block text-sm font-medium" for="payment_method">Select Payment Method</label>
                    <select id="payment_method" name="payment_method" onchange="togglePaymentMethod()" required class="w-full border border-gray-300 rounded px-3 py-2 mt-1">
                        <option value="">-- Select Payment Method --</option>
                        <option value="gcash">GCash</option>
                        <option value="paypal">PayPal</option>
                    </select>
                </div>

                <!-- GCash Number -->
                <div id="gcash_number_field" class="hidden">
                    <label class="block text-sm font-medium" for="gcash_number">GCash Mobile Number</label>
                    <input id="gcash_number" type="text" name="gcash_number" class="w-full border border-gray-300 rounded px-3 py-2 mt-1" />
                </div>

                <!-- GCash QR Code -->
                <div id="gcash_qr" class="hidden">
                    <p class="font-medium mb-1">Scan GCash QR Code:</p>
                    <img src="../image/gcash_qr.png" alt="GCash QR" class="w-48 rounded shadow">
                </div>

                <!-- PayPal QR Code -->
                <div id="paypal_qr" class="hidden">
                    <p class="font-medium mb-1">Scan PayPal QR Code:</p>
                    <img src="../image/paypal_qr.png" alt="PayPal QR" class="w-48 rounded shadow">
                </div>

                <!-- Upload Proof -->
                <div>
                    <label class="block text-sm font-medium" for="proof_image">Upload Proof of Payment</label>
                    <input id="proof_image" type="file" name="proof_image" accept="image/*" required class="w-full border border-gray-300 rounded px-3 py-2 mt-1" />
                </div>

                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
                    Submit Order with Proof
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Scripts -->
<script>
document.addEventListener("DOMContentLoaded", function () {
    let currentImage = 0;
    const images = document.querySelectorAll('.slider-image');

    function showImage(index) {
        images.forEach((img, i) => {
            img.classList.toggle('opacity-100', i === index);
            img.classList.toggle('opacity-0', i !== index);
        });
    }

    window.prevImage = function () {
        currentImage = (currentImage - 1 + images.length) % images.length;
        showImage(currentImage);
    };

    window.nextImage = function () {
        currentImage = (currentImage + 1) % images.length;
        showImage(currentImage);
    };

    window.togglePaymentMethod = function () {
        const paymentMethod = document.getElementById('payment_method').value;
        const gcashField = document.getElementById('gcash_number_field');
        const gcashQR = document.getElementById('gcash_qr');
        const paypalQR = document.getElementById('paypal_qr');

        gcashField.classList.add('hidden');
        gcashQR.classList.add('hidden');
        paypalQR.classList.add('hidden');

        if (paymentMethod === 'gcash') {
            gcashField.classList.remove('hidden');
            gcashQR.classList.remove('hidden');
        } else if (paymentMethod === 'paypal') {
            paypalQR.classList.remove('hidden');
        }
    };
});
</script>
</body>
</html>
