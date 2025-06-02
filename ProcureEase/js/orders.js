
$(document).ready(function () {
  function showLoading() {
    const tbody = $('#ordersTable tbody');
    tbody.html(`
      <tr>
        <td colspan="9" class="text-center py-4">
          <svg class="animate-spin h-8 w-8 mx-auto text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
          </svg>
          <p>Loading orders...</p>
        </td>
      </tr>
    `);
  }

  function getStatusClass(status) {
    const s = status.trim().toLowerCase();
    if (s === 'accepted') return 'text-green-600';
    if (s === 'declined') return 'text-red-600';
    if (s === 'pending') return 'text-yellow-600';
    return '';
  }

  function loadOrders() {
    showLoading();

    $.ajax({
      url: '../supplier/get-orders.php',
      method: 'GET',
      dataType: 'json',
      success: function (response) {
        const tbody = $('#ordersTable tbody');
        tbody.empty();

        if (response.success && response.orders.length) {
          response.orders.forEach(order => {
            const statusClass = getStatusClass(order.status);
            const normalizedStatus = order.status.trim().toLowerCase();

            const actions = normalizedStatus === 'pending'
              ? `
                <div class="flex flex-col sm:flex-row sm:space-x-2 space-y-2 sm:space-y-0">
                  <button class="accept-btn bg-green-500 text-white px-3 py-1 rounded hover:bg-green-600 transition">Accept</button>
                  <button class="decline-btn bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600 transition">Decline</button>
                </div>
              ` : '';

            const row = `
              <tr data-order-id="${order.order_id}" class="hover:bg-gray-50">
                <td class="p-2 border whitespace-nowrap">${order.order_id}</td>
                <td class="p-2 border whitespace-nowrap">${order.full_name}</td>
                <td class="p-2 border whitespace-nowrap">${order.product_name}</td>
                <td class="p-2 border whitespace-nowrap">${order.amount}</td>
                <td class="p-2 border whitespace-nowrap">${order.location}</td>
                <td class="p-2 border whitespace-nowrap">${order.payment_method}</td>
                <td class="p-2 border status-cell ${statusClass} font-semibold whitespace-nowrap">${order.status}</td>
                <td class="p-2 border whitespace-nowrap text-center">
                  <button class="view-details-btn bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600 transition">View Details</button>
                </td>
                <td class="p-2 border whitespace-nowrap">${actions}</td>
              </tr>
            `;
            tbody.append(row);
          });
        } else {
          tbody.html(`
            <tr>
              <td colspan="9" class="text-center p-4 text-gray-500">No orders found.</td>
            </tr>
          `);
        }
      },
      error: function () {
        $('#ordersTable tbody').html(`
          <tr>
            <td colspan="9" class="text-center p-4 text-red-500">Error loading orders. Please try again later.</td>
          </tr>
        `);
      }
    });
  }

  // Accept order
  $('#ordersTable').on('click', '.accept-btn', function () {
    const tr = $(this).closest('tr');
    const orderId = tr.data('order-id');
    const buttons = tr.find('.accept-btn, .decline-btn');
    buttons.prop('disabled', true);

    $.post('../supplier/update-order-status.php', { order_id: orderId, status: 'Accepted' }, function (res) {
      if (res.success) {
        tr.find('.status-cell')
          .text('Accepted')
          .removeClass('text-yellow-600 text-red-600')
          .addClass('text-green-600');
        buttons.remove();
      } else {
        alert('Failed to accept order: ' + res.message);
        buttons.prop('disabled', false);
      }
    }, 'json').fail(function () {
      alert('Error updating order status');
      buttons.prop('disabled', false);
    });
  });

  // Decline order
  $('#ordersTable').on('click', '.decline-btn', function () {
    const tr = $(this).closest('tr');
    const orderId = tr.data('order-id');
    const buttons = tr.find('.accept-btn, .decline-btn');
    buttons.prop('disabled', true);

    $.post('../supplier/update-order-status.php', { order_id: orderId, status: 'Declined' }, function (res) {
      if (res.success) {
        tr.find('.status-cell')
          .text('Declined')
          .removeClass('text-yellow-600 text-green-600')
          .addClass('text-red-600');
        buttons.remove();
      } else {
        alert('Failed to decline order: ' + res.message);
        buttons.prop('disabled', false);
      }
    }, 'json').fail(function () {
      alert('Error updating order status');
      buttons.prop('disabled', false);
    });
  });

  // View details modal logic
  $('#ordersTable').on('click', '.view-details-btn', function () {
    const orderId = $(this).closest('tr').data('order-id');

    $.get('../supplier/get-order-details.php', { order_id: orderId }, function (res) {
      if (res.success) {
        showModal(res.order);
      } else {
        alert('Failed to get order details: ' + res.message);
      }
    }, 'json').fail(function () {
      alert('Error fetching order details');
    });
  });

  $('#closeModalBtn').on('click', closeModal);
  $('#orderDetailsModal').on('click', function (e) {
    if (e.target.id === 'orderDetailsModal') closeModal();
  });

  function closeModal() {
    $('#orderDetailsModal').addClass('hidden').removeClass('flex');
  }

  // Load orders on page load
  loadOrders();
});

function showModal(order) {
  const modal = $('#orderDetailsModal');
  const content = $('#modalContent');

  content.html(`
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <div><strong>Order ID:</strong> <span class="block">${order.order_id}</span></div>
      <div><strong>Full Name:</strong> <span class="block">${order.full_name}</span></div>
      <div><strong>Product Name:</strong> <span class="block">${order.product_name}</span></div>
      <div><strong>Amount:</strong> <span class="block">${order.amount}</span></div>
      <div><strong>Location:</strong> <span class="block">${order.location}</span></div>
      <div><strong>Agency:</strong> <span class="block">${order.agency || 'N/A'}</span></div>
      <div><strong>Payment Method:</strong> <span class="block">${order.payment_method}</span></div>
      <div><strong>Status:</strong> <span class="block">${order.status}</span></div>
    </div>

    <div class="mt-4">
      <strong>Proof of Payment:</strong>
      <img src="../uploads/proof/${order.proof_of_payment}" alt="Proof of Payment" class="mt-2 max-w-full h-auto rounded border" />
    </div>
  `);

  modal.removeClass('hidden').addClass('flex');
}