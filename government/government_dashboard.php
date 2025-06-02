<?php
include('../includes/db_connect.php');
session_start();

// Access Control
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'government') {
    header('Location: login.php');
    exit();
}


$government_id = $_SESSION['user_id'];
$page = isset($_GET['page']) ? $_GET['page'] : 'home';

$subscribed = false;
$today = date('Y-m-d');

$query = "SELECT * FROM subscription_payments WHERE user_id = ? AND status = 'approved' AND ? BETWEEN start_date AND end_date LIMIT 1";
$stmt = $conn->prepare($query);
$stmt->bind_param("is", $government_id, $today);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
    $subscribed = true;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Government Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/feather-icons"></script>
</head>
<body class="bg-gray-100">

<!-- Navbar -->
<nav class="bg-gray-800 p-4 text-white">
    <div class="flex justify-between items-center">
        <div class="font-bold text-2xl text-amber-400">ProcureEase</div>
        <div class="space-x-4">
            <a href="?page=home" class="hover:text-amber-400">Home</a>
            <a href="?page=products" class="hover:text-amber-400">Products</a>
            <a href="?page=orders" class="hover:text-amber-400">Orders</a>
            <a href="?page=profile" class="hover:text-amber-400">Profile</a>
            <a href="../logout.php" class="bg-red-500 px-4 py-2 rounded hover:bg-red-600">Logout</a>
        </div>
    </div>
</nav>

<?php if (!$subscribed): ?>
<!-- Subscription Banner -->
<div id="subscriptionBanner" class="bg-yellow-400 cursor-pointer text-black py-3 px-4">
    You are viewing limited product suggestions. Please 
    <a href="#" id="subscriptionLink" class="underline text-blue-700">subscribe</a> to see full access.
</div>

<!-- Subscription Plan Modal -->
<div id="subscriptionModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-lg max-w-lg w-full p-6 relative shadow-xl">
        <button id="closeSubscriptionModal" class="absolute top-3 right-3 text-gray-500 hover:text-gray-700 text-3xl font-bold leading-none">&times;</button>
        <h2 class="text-2xl font-bold mb-4 text-amber-600">Choose a Subscription Plan</h2>

        <p class="mb-6 text-gray-700 border rounded-lg p-4 shadow hover:shadow-md transition cursor-pointer">
            <strong>Free Plan</strong> <br>
            View up to 5 posted products. Upgrade to Premium for unlimited access:
        </p>

        <div class="space-y-4">
            <div onclick="openPaymentForm('Premium Monthly', 499)" class="border rounded-lg p-4 shadow hover:shadow-md transition cursor-pointer">
                <h3 class="text-xl font-semibold text-gray-800">Premium Monthly Plan</h3>
                <p class="text-gray-600 mb-2">₱499 / 30 days - Unlimited product views</p>
                <span class="bg-amber-500 hover:bg-amber-700 text-white px-4 py-2 rounded inline-block mt-2">Subscribe Monthly</span>
            </div>

            <div onclick="openPaymentForm('Premium Yearly', 4999)" class="border rounded-lg p-4 shadow hover:shadow-md transition cursor-pointer">
                <h3 class="text-xl font-semibold text-gray-800">Premium Yearly Plan</h3>
                <p class="text-gray-600 mb-2">₱4,999 / 365 days - Save ₱989 with yearly subscription</p>
                <span class="bg-amber-500 hover:bg-amber-700 text-white px-4 py-2 rounded inline-block mt-2">Subscribe Yearly</span>
            </div>
        </div>
    </div>
</div>
<!-- Payment Form Modal -->
<div id="paymentModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-lg w-full max-w-4xl p-6 relative shadow-xl">
        <button id="closePaymentModal" class="absolute top-3 right-3 text-gray-500 hover:text-gray-700 text-3xl font-bold leading-none">&times;</button>
        <h2 class="text-2xl font-bold mb-4 text-amber-600">Complete Payment</h2>
       <form id="subscriptionForm" class="grid grid-cols-1 md:grid-cols-2 gap-6" enctype="multipart/form-data">
            <input type="hidden" id="plan_type" name="plan_type">
            <input type="hidden" id="plan_amount" name="plan_amount">

            <div>
                <label class="block font-semibold text-gray-700">Subscription Type:</label>
                <input type="text" id="display_plan_type" class="w-full border px-3 py-2 rounded" disabled>
            </div>

            <div>
                <label class="block font-semibold text-gray-700">Amount:</label>
                <input type="text" id="display_plan_amount" class="w-full border px-3 py-2 rounded" disabled>
            </div>

            <div>
                <label class="block font-semibold text-gray-700">Full Name:</label>
                <input type="text" name="full_name" class="w-full border px-3 py-2 rounded" required>
            </div>

            <div>
                <label class="block font-semibold text-gray-700">Contact Number:</label>
                <input type="text" name="contact_number" class="w-full border px-3 py-2 rounded" required>
            </div>

            <div>
                <label class="block font-semibold text-gray-700">Payment Method:</label>
                <select name="payment_method" class="w-full border px-3 py-2 rounded" required>
                    <option value="">-- Select --</option>
                    <option value="gcash">GCash</option>
                    <option value="paypal">PayPal</option>
                </select>
            </div>

            <div>
                <label class="block font-semibold text-gray-700">Amount Sent:</label>
                <input type="number" name="amount_sent" class="w-full border px-3 py-2 rounded" required>
            </div>

            <!-- GCash QR Code -->
            <div id="gcashQRCode" class="md:col-span-2 mt-4 hidden">
                <label class="block font-semibold text-gray-700 mb-2">Scan this GCash QR Code:</label>
                <img src="../image/gcash_qr.png" alt="GCash QR Code" class="mx-auto w-32 object-contain" />
            </div>

            <!-- PayPal QR Code -->
            <div id="paypalQRCode" class="md:col-span-2 mt-4 hidden">
                <label class="block font-semibold text-gray-700 mb-2">Pay with PayPal using this QR Code:</label>
                <img src="../image/paypal_qr.png" alt="PayPal QR Code" class="mx-auto w-32 object-contain" />
            </div>

            <div class="md:col-span-2">
                <label class="block font-semibold text-gray-700">Upload Proof of Payment:</label>
                <input type="file" name="proof_of_payment" accept="image/*" class="w-full border px-3 py-2 rounded" required>
            </div>

            <div class="md:col-span-2 text-right">
        <button type="submit" class="bg-amber-500 hover:bg-amber-700 text-white px-4 py-2 rounded">Submit</button>
    </div>
        </form>
    </div>
</div>
<?php endif; ?>

<!-- Main Content -->
<main class="bg-gray-100 p-6 min-h-screen">
    <?php
    switch ($page) {
    case 'home':
    $suggestions_query = "
        SELECT p.*, s.company_name 
        FROM products p 
        LEFT JOIN suppliers s ON p.user_id = s.user_id
        ORDER BY p.created_at DESC LIMIT 3
    ";
    $suggestions_result = mysqli_query($conn, $suggestions_query);
    ?>
<!--Improved Hero Section -->
<section class="flex flex-col-reverse lg:flex-row items-center gap-16 mb-16 px-4 max-w-7xl mx-auto">
    <div class="lg:w-1/2 text-center lg:text-left">
        <h1 class="text-5xl font-extrabold leading-tight text-amber-600 mb-6">
            Streamline Government Procurement with ProcureEase
        </h1>
        <p class="text-lg text-gray-700 mb-10 max-w-xl mx-auto lg:mx-0">
            Connect with trusted suppliers, simplify purchasing, and maintain full transparency on all transactions.
        </p>
        <a href="?page=products" class="inline-block bg-amber-500 text-white px-10 py-4 rounded-lg font-semibold shadow-lg hover:bg-amber-600 transition">
            Browse Products
        </a>
    </div>
    <div class="lg:w-1/2">
<img src="../image/logo2.png" alt="Government procurement" class="rounded-lg shadow-lg mx-auto max-w-64" /></section>

<!-- ======= Improved Features Section ======= -->
<section class="mb-16 px-4 max-w-7xl mx-auto">
    <h2 class="text-3xl font-bold text-amber-600 mb-12 text-center">Why Choose ProcureEase?</h2>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-10">
        <div class="bg-white rounded-lg p-8 shadow-md hover:shadow-lg transition flex flex-col items-center text-center">
            <div class="text-amber-500 mb-6">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-14 w-14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                </svg>
            </div>
            <h3 class="text-xl font-semibold mb-3">Transparent Transactions</h3>
            <p class="text-gray-600 max-w-xs">Clear audit trails for every purchase to maintain accountability and trust.</p>
        </div>
        <div class="bg-white rounded-lg p-8 shadow-md hover:shadow-lg transition flex flex-col items-center text-center">
            <div class="text-amber-500 mb-6">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-14 w-14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 21v-2" />
                </svg>
            </div>
            <h3 class="text-xl font-semibold mb-3">Efficient Procurement</h3>
            <p class="text-gray-600 max-w-xs">Simplify processes with an easy-to-use platform tailored for government.</p>
        </div>
        <div class="bg-white rounded-lg p-8 shadow-md hover:shadow-lg transition flex flex-col items-center text-center">
            <div class="text-amber-500 mb-6">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-14 w-14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <h3 class="text-xl font-semibold mb-3">Trusted Suppliers</h3>
            <p class="text-gray-600 max-w-xs">Access a vetted network ensuring quality and fair pricing.</p>
        </div>
    </div>
</section>

<section aria-labelledby="suggested-products-title" class="max-w-6xl mx-auto px-6 py-12">
    <h2 id="suggested-products-title" class="text-3xl font-extrabold text-amber-600 mb-12 text-center">
        Latest Products
    </h2>

    <div id="productContainer" class="grid grid-cols-1 md:grid-cols-3 gap-10">
        <?php while($product = mysqli_fetch_assoc($suggestions_result)): ?>
            <?php
            $image_query = "SELECT image_path FROM product_images WHERE product_id = " . intval($product['product_id']) . " LIMIT 1";
            $image_result = mysqli_query($conn, $image_query);
            $image = mysqli_fetch_assoc($image_result);

            $images_query = "SELECT image_path FROM product_images WHERE product_id = " . intval($product['product_id']);
            $images_result = mysqli_query($conn, $images_query);
            $images = [];
            while($img = mysqli_fetch_assoc($images_result)) {
                $images[] = $img['image_path'];
            }
            ?>
            <article class="bg-white rounded-lg border border-gray-200 hover:border-amber-500 transition-colors duration-200 flex flex-col">
                <?php if ($image): ?>
                    <img
                        src="../uploads/<?= htmlspecialchars($image['image_path']) ?>"
                        alt="<?= htmlspecialchars($product['product_name']) ?>"
                        class="w-full h-40 object-cover rounded-t-lg"
                        loading="lazy"
                    />
                <?php else: ?>
                    <div class="flex items-center justify-center h-40 bg-gray-50 rounded-t-lg text-gray-400 text-sm font-medium">
                        No Image Available
                    </div>
                <?php endif; ?>

                <div class="p-5 flex flex-col items-center text-center space-y-4">
                    <h3 class="text-lg font-semibold text-gray-900">
                        <?= htmlspecialchars($product['product_name']) ?>
                    </h3>
                    <p class="text-green-700 font-medium text-lg">₱<?= number_format($product['product_price'], 2) ?></p>
                    <button
                        type="button"
                        class="text-amber-600 font-semibold hover:underline focus:outline-none"
                        data-product='<?= json_encode([
                            'id' => $product['product_id'],
                            'name' => $product['product_name'],
                            'description' => $product['product_description'],
                            'price' => number_format($product['product_price'], 2),
                            'images' => $images,
                            'supplier' => $product['company_name'] ?? 'Unknown Supplier'
                        ], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT) ?>'
                        aria-label="View details for <?= htmlspecialchars($product['product_name']) ?>"
                    >
                        View Details
                    </button>
                    <p class="text-xs text-gray-500 italic mt-1">
                        Supplier: <?= htmlspecialchars($product['company_name'] ?? 'Unknown Supplier') ?>
                    </p>
                </div>
            </article>
        <?php endwhile; ?>
    </div>
</section>
<?php
break;
        case 'products':
            include 'government_viewproducts.php';
            break;
        case 'orders':
            include 'government_orders.php';
            break;
        case 'subscription':
            include 'government_subscription.php';
            break;

        case 'profile':
            include 'government_profile.php';
            break;

        default:
            echo "<h2 class='text-3xl font-semibold mb-8 text-red-500'>Invalid Page</h2>";
    }
    ?>
</main>

<footer class="bg-gray-100 text-gray-700 py-8 mt-16">
  <div class="max-w-6xl mx-auto px-6 flex flex-col md:flex-row justify-between items-center">
    <p class="text-sm">&copy; <?= date('Y') ?> ProcureEase. All rights reserved.</p>
    <nav class="space-x-6 mt-4 md:mt-0">
      <a href="?page=about" class="hover:underline">About</a>
      <a href="?page=contact" class="hover:underline">Contact</a>
      <a href="?page=privacy" class="hover:underline">Privacy Policy</a>
    </nav>
  </div>
</footer>


<!-- Product View Modal -->
<div id="productViewModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-lg max-w-3xl w-full p-6 relative shadow-lg overflow-auto max-h-[90vh]">
        <button id="closeProductModal" class="absolute top-3 right-3 text-gray-500 hover:text-gray-700 text-3xl font-bold leading-none">&times;</button>
        <p id="modalSupplierName" class="mb-2 italic text-gray-600"></p>
        <div id="modalImages" class="flex overflow-x-auto space-x-4 mb-4"></div>
        <h2 id="modalProductName" class="text-3xl font-bold mb-2"></h2>
        <p id="modalProductDesc" class="mb-4 text-gray-700"></p>
        <p id="modalProductPrice" class="text-xl font-semibold"></p>
    </div>
</div>

<script>
feather.replace();

// PRODUCT VIEW MODAL
document.querySelectorAll('.view-product-btn').forEach(button => {
    button.addEventListener('click', () => {
        const modal = document.getElementById('productViewModal');
        const productData = JSON.parse(button.dataset.product);

        document.getElementById('modalProductName').textContent = productData.name;
        document.getElementById('modalSupplierName').textContent = "Supplier: " + productData.supplier;
        document.getElementById('modalProductDesc').textContent = productData.description;
        document.getElementById('modalProductPrice').textContent = '₱' + productData.price;

        const imagesContainer = document.getElementById('modalImages');
        imagesContainer.innerHTML = '';

        if (productData.images.length > 0) {
            productData.images.forEach(imgPath => {
                const img = document.createElement('img');
                img.src = '../uploads/' + imgPath;
                img.alt = productData.name;
                img.className = 'w-48 h-48 object-cover rounded shadow';
                imagesContainer.appendChild(img);
            });
        } else {
            const img = document.createElement('img');
            img.src = '../uploads/no-image.png';
            img.alt = 'No image available';
            img.className = 'w-48 h-48 object-cover rounded shadow';
            imagesContainer.appendChild(img);
        }

        modal.classList.remove('hidden');
    });
});

// CLOSE MODALS
document.getElementById('closeProductModal')?.addEventListener('click', () => {
    document.getElementById('productViewModal').classList.add('hidden');
});

document.getElementById('subscriptionLink')?.addEventListener('click', (e) => {
    e.preventDefault();
    document.getElementById('subscriptionModal').classList.remove('hidden');
});

document.getElementById('closeSubscriptionModal')?.addEventListener('click', () => {
    document.getElementById('subscriptionModal').classList.add('hidden');
});

document.getElementById('closePaymentModal')?.addEventListener('click', () => {
    document.getElementById('paymentModal').classList.add('hidden');
});

// OPEN PAYMENT FORM FUNCTION (fixes your error)
function openPaymentForm(plan, amount) {
    document.getElementById('plan_type').value = plan;
    document.getElementById('plan_amount').value = amount;
    document.getElementById('display_plan_type').value = plan;
    document.getElementById('display_plan_amount').value = '₱' + amount;

    // Hide the subscription modal and show the payment modal
    document.getElementById('subscriptionModal').classList.add('hidden');
    document.getElementById('paymentModal').classList.remove('hidden');
}

// PAYMENT METHOD QR CODE TOGGLE
const paymentMethodSelect = document.querySelector('select[name="payment_method"]');
const gcashQRCodeDiv = document.getElementById('gcashQRCode');
const paypalQRCodeDiv = document.getElementById('paypalQRCode');

if(paymentMethodSelect) {
    paymentMethodSelect.addEventListener('change', () => {
        if (paymentMethodSelect.value === 'gcash') {
            gcashQRCodeDiv.classList.remove('hidden');
            paypalQRCodeDiv.classList.add('hidden');
        } else if (paymentMethodSelect.value === 'paypal') {
            paypalQRCodeDiv.classList.remove('hidden');
            gcashQRCodeDiv.classList.add('hidden');
        } else {
            gcashQRCodeDiv.classList.add('hidden');
            paypalQRCodeDiv.classList.add('hidden');
        }
    });
}
</script>

<script>
document.getElementById('subscriptionForm').addEventListener('submit', function (e) {
    e.preventDefault();

    const form = e.target;
    const formData = new FormData(form);
    formData.set('plan_type', document.getElementById('plan_type').value);
    formData.set('plan_amount', document.getElementById('plan_amount').value);

    fetch('government_subscription.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        alert(data.message);
        if (data.success) {
            document.getElementById('paymentModal').classList.add('hidden');
            form.reset();
        }
    })
    .catch(err => {
        console.error('Submission error:', err);
        alert('Something went wrong.');
    });
});
</script>
</body>
</html>
