$(document).ready(function () {
  const userId = $('#userId').val(); // Make sure <input type="hidden" id="userId" value="123"> exists

  function loadSalesData() {
    const tbody = $('#salesTable tbody');
    tbody.html('<tr><td colspan="4" class="text-center py-4 text-gray-400">Loading...</td></tr>');

    $.get('../supplier/get-sales-data.php', { user_id: userId }, function (res) {
      tbody.empty();

      if (res.success && res.sales.length > 0) {
        res.sales.forEach((sale, i) => {
          const rowClass = i % 2 === 0 ? '' : 'bg-gray-50';
          tbody.append(`
            <tr class="border-t ${rowClass}">
              <td class="px-4 py-2">${sale.product}</td>
              <td class="px-4 py-2">${sale.quantity}</td>
              <td class="px-4 py-2">₱${sale.revenue}</td>
              <td class="px-4 py-2">${sale.date}</td>
            </tr>
          `);
        });
      } else {
        tbody.html('<tr><td colspan="4" class="text-center py-4 text-gray-500">No sales found.</td></tr>');
      }
    }).fail(() => {
      tbody.html('<tr><td colspan="4" class="text-center py-4 text-red-500">Failed to load sales data.</td></tr>');
    });
  }

  loadSalesData();
});
