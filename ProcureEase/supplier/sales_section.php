<input type="hidden" id="userId" value="<?php echo $_SESSION['user_id']; ?>">


<div class="bg-white p-6 rounded shadow">
  <h2 class="text-2xl font-bold mb-4 text-gray-700">Sales Overview</h2>
  <p class="text-gray-600 mb-4">Track your sales performance below:</p>

  <div class="overflow-x-auto">
    <table id="salesTable" class="w-full border text-sm text-left">
      <thead class="bg-gray-100 text-gray-700">
        <tr>
          <th class="px-4 py-2 border">Product</th>
          <th class="px-4 py-2 border">Quantity Sold</th>
          <th class="px-4 py-2 border">Revenue</th>
          <th class="px-4 py-2 border">Date</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td colspan="4" class="text-center py-4 text-gray-400">Loading...</td>
        </tr>
      </tbody>
    </table>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="../js/sales.js"></script>
