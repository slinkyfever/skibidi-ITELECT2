document.addEventListener('DOMContentLoaded', () => {
  const form = document.getElementById('editProductForm');

  form.addEventListener('submit', (e) => {
    e.preventDefault();

    const name = document.getElementById('modalName').value.trim();
    const description = document.getElementById('modalDescription').value.trim();
    const price = parseFloat(document.getElementById('modalPrice').value);
    const quantity = parseInt(document.getElementById('modalQuantity').value, 10);

    if (!window.currentProductId || !name || !description || isNaN(price) || isNaN(quantity)) {
      alert("Please complete all fields correctly.");
      return;
    }

    fetch('../supplier/update_product.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        product_id: currentProductId,
        product_name: name,
        product_description: description,
        product_price: price,
        product_quantity: quantity
      }),
    })
    .then(res => res.json())
    .then(data => {
      if (data.success) {
        alert("Product updated successfully.");
        location.reload();
      } else {
        alert("Update failed: " + data.message);
      }
    })
    .catch(err => {
      console.error("Error:", err);
      alert("An error occurred while updating the product.");
    });
  });
});