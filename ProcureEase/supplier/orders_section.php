<div class="bg-white p-6 rounded-lg shadow-md">
  <h2 class="text-2xl font-bold text-gray-800 mb-4">Orders</h2>
  <p class="text-gray-600 mb-4">This section will show all your received orders.</p>

  <table class="w-full text-left border-collapse" id="ordersTable">
    <thead>
      <tr class="bg-gray-100">
        <th class="p-2 border">Order ID</th>
        <th class="p-2 border">Customer Name</th>
        <th class="p-2 border">Product</th>
        <th class="p-2 border">Amount</th>
        <th class="p-2 border">Location</th>
        <th class="p-2 border">Payment Method</th>
        <th class="p-2 border">Status</th>
        <th class="p-2 border">Proof of Payment</th>
        <th class="p-2 border">Actions</th>
      </tr>
    </thead>
    <tbody>
      <!-- Orders will be dynamically loaded here -->
    </tbody>
  </table>
</div>

<!-- Modal backdrop -->
<div id="orderDetailsModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
  <!-- Modal content -->
  <div class="bg-white rounded-lg shadow-lg max-w-lg w-full p-6 relative">
    <button id="closeModalBtn" class="absolute top-2 right-2 text-gray-500 hover:text-gray-700 text-2xl font-bold">&times;</button>
    <h2 class="text-xl font-semibold mb-4">Order Details</h2>
    <div id="modalContent" class="space-y-3">
      <!-- Dynamic content will go here -->
    </div>
  </div>
</div>

<!-- jQuery CDN -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="../js/orders.js"></script>
