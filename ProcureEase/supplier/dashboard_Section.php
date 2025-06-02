<?php
include('../includes/db_connect.php');

$showSubscriptionNotice = true;
if (isset($_SESSION['user_id'])) {
  $user_id = $_SESSION['user_id'];

  $stmt = $conn->prepare("SELECT status, end_date FROM subscription_payments WHERE user_id = ? ORDER BY id DESC LIMIT 1");
  $stmt->bind_param("i", $user_id);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($row = $result->fetch_assoc()) {
    $status = $row['status'];
    $end_date = $row['end_date'];
    $today = date('Y-m-d');

    if ($status === 'approved' && $end_date >= $today) {
      $showSubscriptionNotice = false;
    }
  }

  $stmt->close();
}
?>

<h2 class="text-3xl font-bold mb-6 text-amber-500">Dashboard</h2>
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
  <div class="bg-white p-6 rounded-lg shadow flex items-center space-x-4">
    <div class="bg-amber-500 p-3 rounded-full">
      <i data-feather="box" class="text-white"></i>
    </div>
    <div>
      <h4 class="text-lg font-semibold">Total Products</h4>
      <p class="text-gray-600" id="totalProductCount">Loading...</p>

    </div>
  </div>
  <div class="bg-white p-6 rounded-lg shadow flex items-center space-x-4">
    <div class="bg-amber-500 p-3 rounded-full">
      <i data-feather="user-check" class="text-white"></i>
    </div>
    <div>
      <h4 class="text-lg font-semibold">Profile Status</h4>
      <p class="text-gray-600">Complete</p>
    </div>
  </div>
  <div class="bg-white p-6 rounded-lg shadow flex items-center space-x-4">
    <div class="bg-amber-500 p-3 rounded-full">
      <i data-feather="activity" class="text-white"></i>
    </div>
    <div>
      <h4 class="text-lg font-semibold">Total Sales</h4>
      <p class="text-gray-600">1123</p>
    </div>
  </div>
  <div class="bg-white p-6 rounded-lg shadow flex items-center space-x-4">
    <div class="bg-amber-500 p-3 rounded-full">
      <i data-feather="shopping-cart" class="text-white"></i>
    </div>
    <div>
      <h4 class="text-lg font-semibold">New Orders</h4>
      <p class="text-gray-600" id="newOrderCount">Loading...</p>
    </div>
  </div>
</div>
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-10">
  <!-- Left: Order Status -->
  <div class="bg-white p-6 rounded-lg shadow">
    <h3 class="text-2xl font-bold mb-4">Latest Order Status</h3>
    <div class="space-y-4">
      <div class="flex items-center justify-between p-4 bg-gray-100 rounded-lg">
        <span class="font-medium">Order #2345</span>
        <span class="text-green-600 font-semibold">Received by Government</span>
      </div>
      <div class="flex items-center justify-between p-4 bg-gray-100 rounded-lg">
        <span class="font-medium">Order #2346</span>
        <span class="text-yellow-500 font-semibold">In Transit</span>
      </div>
      <div class="flex items-center justify-between p-4 bg-gray-100 rounded-lg">
        <span class="font-medium">Order #2347</span>
        <span class="text-red-500 font-semibold">Pending Dispatch</span>
      </div>
    </div>
  </div>

  <!-- Right: Sales Chart -->
  <div class="bg-white p-6 rounded-lg shadow">
    <h3 class="text-2xl font-bold mb-4">Sales Overview</h3>
    <canvas id="salesChart" height="100"></canvas>
  </div>
</div>

<?php if ($showSubscriptionNotice): ?>
  <div class="bg-yellow-200 p-4 mt-4 rounded text-yellow-800">
    <p>You are viewing limited product suggestions.
      <a href="javascript:void(0)" onclick="openModal('subscriptionModal')" class="underline text-amber-500">Subscribe now</a>
      to unlock full access.
    </p>
  </div>
<?php endif; ?>

<!-- Subscription Modal -->
<div id="subscriptionModal" class="fixed inset-0 z-50 hidden bg-black bg-opacity-50 flex items-center justify-center">
  <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6 relative">
    <h3 class="text-xl font-bold mb-4 text-amber-600">Choose a Subscription Plan</h3>
    <ul class="space-y-4">
      <li class="border p-4 rounded hover:shadow">
        <h4 class="font-semibold">Free Plan</h4>
        <p class="text-sm text-gray-600">Post up to 5 products.</p>
      </li>
      <li class="border p-4 rounded hover:shadow cursor-pointer bg-amber-100 hover:bg-amber-200 transition">
        <h4 class="font-semibold text-amber-700">Premium Plan - ₱499/month</h4>
        <p class="text-sm text-gray-700">Unlimited product postings and priority visibility.</p>
        <button class="mt-2 bg-amber-500 text-white px-4 py-2 rounded hover:bg-amber-600" onclick="openPaymentModal('monthly')">Subscribe Monthly</button>
      </li>
      <li class="border p-4 rounded hover:shadow cursor-pointer bg-amber-100 hover:bg-amber-200 transition">
        <h4 class="font-semibold text-amber-700">Premium Plan - ₱4,999/year</h4>
        <p class="text-sm text-gray-700">Save ₱989 with a yearly subscription. Unlimited product postings and priority visibility.</p>
        <button class="mt-2 bg-amber-500 text-white px-4 py-2 rounded hover:bg-amber-600" onclick="openPaymentModal('yearly')">Subscribe Yearly</button>
      </li>
    </ul>
    <button onclick="closeModal('subscriptionModal')" class="absolute top-2 right-2 text-gray-500 hover:text-black text-xl">&times;</button>
  </div>
</div>

<!-- Payment Modal -->
<div id="paymentModal" class="fixed inset-0 z-50 hidden bg-black bg-opacity-50 flex items-center justify-center px-4">
  <div class="bg-white p-6 rounded-lg w-full max-w-4xl relative max-h-[90vh]">
    <h3 class="text-xl font-bold mb-4 text-amber-600">Complete Your Payment</h3>

    <form id="paymentForm" enctype="multipart/form-data">
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <!-- Left Column -->
        <div>
          <!-- Subscription Info -->
          <div class="mb-4">
            <label class="block mb-1 font-medium">Subscription Type</label>
            <input type="text" id="selectedPlan" name="plan" class="w-full p-2 border rounded bg-gray-100" readonly>
          </div>

          <div class="mb-4">
            <label class="block mb-1 font-medium">Amount (₱)</label>
            <input type="text" id="subscriptionAmount" name="amount" class="w-full p-2 border rounded bg-gray-100" readonly>
          </div>

          <!-- Subscriber Info -->
          <div class="mb-4">
            <label class="block mb-1 font-medium">Your Name</label>
            <input type="text" name="name" required class="w-full p-2 border rounded" placeholder="Juan Dela Cruz">
          </div>

          <div class="mb-4">
            <label class="block mb-1 font-medium">Contact Number</label>
            <input type="tel" name="contact" required class="w-full p-2 border rounded" placeholder="09XXXXXXXXX">
          </div>
        </div>

        <!-- Right Column -->
        <div>
          <!-- Payment Method -->
          <div class="mb-4">
            <label class="block mb-1 font-medium">Select Payment Method</label>
            <select id="paymentMethod" name="payment_method" class="w-full p-2 border rounded" onchange="toggleQRCode(this.value)" required>
              <option value="">-- Choose Payment Method --</option>
              <option value="gcash">GCash</option>
              <option value="paypal">PayPal</option>
            </select>
          </div>

          <!-- GCash Info -->
          <div id="gcashInfo" class="hidden mb-4 bg-gray-50 p-4 rounded border">
            <p class="font-medium mb-2">Scan this QR Code or send to the number below:</p>
            <img src="../image/gcash_qr.png" alt="GCash QR Code" class="w-32 mb-2">
            <p class="text-sm text-gray-700">GCash Number: <strong>0917-XXXXXXX</strong></p>
          </div>

          <!-- PayPal Info -->
          <div id="paypalInfo" class="hidden mb-4 bg-gray-50 p-4 rounded border">
            <p class="font-medium mb-2">Scan this PayPal QR Code or send to the email below:</p>
            <img src="../image/paypal_qr.png" alt="PayPal QR Code" class="w-32 mb-2">
            <p class="text-sm text-gray-700">PayPal Email: <strong>your-email@paypal.com</strong></p>
          </div>

          <!-- Amount Sent by Subscriber -->
          <div class="mb-4">
            <label class="block mb-1 font-medium">Amount Sent (₱)</label>
            <input type="number" name="amount_sent" class="w-full border rounded p-2" placeholder="Enter the amount you paid" min="1" required>
          </div>

          <!-- Proof of Payment -->
          <div class="mb-4">
            <label class="block mb-1 font-medium">Upload Proof of Payment</label>
            <input type="file" name="proof" class="w-full border rounded p-2" accept="image/*,application/pdf" required>
          </div>
        </div>
      </div>

      <!-- Buttons -->
      <div class="flex justify-end space-x-2 mt-4">
        <button type="button" onclick="closeModal('paymentModal')" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">Cancel</button>
        <button type="submit" class="px-4 py-2 bg-amber-500 text-white rounded hover:bg-amber-600">Submit</button>
      </div>
    </form>

    <!-- Close X -->
    <button onclick="closeModal('paymentModal')" class="absolute top-2 right-2 text-gray-500 hover:text-black text-xl">&times;</button>
  </div>
</div>




<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="../js/submit_payment.js"></script>
<script>
  document.addEventListener('DOMContentLoaded', () => {
  loadTotalProductCount();
  loadPendingOrders();
  renderSalesChart();
  setupModalListeners();
});

  /** Load total products from PHP and update UI */
  function loadTotalProductCount() {
    fetch('total_products.php')
      .then(res => res.json())
      .then(data => {
        const totalCount = data.total ?? 0;
        document.getElementById('totalProductCount').textContent =
          `You have ${totalCount} product${totalCount !== 1 ? 's' : ''}`;
      })
      .catch(err => {
        console.error('Failed to load total products:', err);
        document.getElementById('totalProductCount').textContent = 'Failed to load count';
      });
  }

  /** Render the line chart for sales */
 function renderSalesChart() {
  const ctx = document.getElementById('salesChart')?.getContext('2d');
  if (!ctx) return;

  const urlParams = new URLSearchParams(window.location.search);
  const productId = urlParams.get('product_id') 

  fetch(`../supplier/get-sales-chart.php?product_id=${productId}`)
    .then(res => res.json())
    .then((data) => {
      if (data.error) {
        console.error('Chart error:', data.error);
        return;
      }

      new Chart(ctx, {
        type: 'line',
        data: {
          labels: data.labels,
          datasets: [{
            label: 'Total Sales',
            data: data.data,
            backgroundColor: 'rgba(251, 191, 36, 0.2)',
            borderColor: 'rgba(251, 191, 36, 1)',
            borderWidth: 2,
            tension: 0.3,
            fill: true,
            pointRadius: 5,
            pointBackgroundColor: 'rgba(251, 191, 36, 1)'
          }]
        },
        options: {
          responsive: true,
          scales: {
            y: {
              beginAtZero: true,
              title: {
                display: true,
                text: 'Sales (₱)'
              }
            },
            x: {
              title: {
                display: true,
                text: 'Month'
              }
            }
          }
        }
      });
    })
    .catch(err => {
      console.error('Error fetching chart data:', err);
    });
}

  /** Setup modal click-outside to close */
  function setupModalListeners() {
    document.querySelectorAll('.fixed.inset-0.z-50').forEach(modal => {
      modal.addEventListener('click', (e) => {
        const modalContent = modal.querySelector('.bg-white');
        if (!modalContent.contains(e.target)) {
          modal.classList.add('hidden');
        }
      });
    });
  }

  /** Open modal by ID */
  function openModal(id) {
    document.getElementById(id)?.classList.remove('hidden');
  }

  /** Close modal by ID */
  function closeModal(id) {
    document.getElementById(id)?.classList.add('hidden');
  }

  /** Open payment modal and fill data */
  function openPaymentModal(planType) {
    const planInput = document.getElementById('selectedPlan');
    const amountInput = document.getElementById('subscriptionAmount');

    const plans = {
      monthly: {
        name: 'Premium - Monthly',
        price: '499'
      },
      yearly: {
        name: 'Premium - Yearly',
        price: '4999'
      }
    };

    if (plans[planType]) {
      planInput.value = plans[planType].name;
      amountInput.value = plans[planType].price;
    }

    closeModal('subscriptionModal');
    openModal('paymentModal');
  }

  /** Show QR section based on selected method */
  function toggleQRCode(method) {
    const gcash = document.getElementById('gcashInfo');
    const paypal = document.getElementById('paypalInfo');

    gcash?.classList.add('hidden');
    paypal?.classList.add('hidden');

    if (method === 'gcash') gcash?.classList.remove('hidden');
    if (method === 'paypal') paypal?.classList.remove('hidden');
  }

  /** Fetch total pending orders and update UI */
function loadPendingOrders() {
  fetch('pending_orders.php')
    .then(res => res.json())
    .then(data => {
      const total = data.total ?? 0;
      document.getElementById('newOrderCount').textContent = `${total} new order${total !== 1 ? 's' : ''}`;
    })
    .catch(err => {
      console.error('Failed to load pending orders:', err);
      document.getElementById('newOrderCount').textContent = 'Failed to load count';
    });
}
</script>