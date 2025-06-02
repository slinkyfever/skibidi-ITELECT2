<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>View Products</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-6">

<h1 class="text-2xl font-bold text-amber-600 mb-6">Posted Products</h1>

<div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
  <input type="text" id="searchQuery" placeholder="Search product name or description..."
    class="flex-grow border p-2 rounded border-gray-300 min-w-[200px]" />

  <div class="flex gap-2 flex-wrap">
    <input type="number" id="minPrice" placeholder="Min Price" class="w-28 border p-2 rounded border-gray-300" />
    <input type="number" id="maxPrice" placeholder="Max Price" class="w-28 border p-2 rounded border-gray-300" />
    <button onclick="fetchProducts()" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">Filter</button>
  </div>
</div>

<div id="productContainer" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    <!-- Products will be loaded here by AJAX -->
</div>

<script>
async function fetchProducts() {
  try {
    const query = document.getElementById('searchQuery').value;
    const minPrice = document.getElementById('minPrice').value;
    const maxPrice = document.getElementById('maxPrice').value;

    const params = new URLSearchParams({
      q: query,
      min: minPrice,
      max: maxPrice
    });

    const response = await fetch('government_fetch_products.php?' + params.toString());
    const products = await response.json();

    const container = document.getElementById('productContainer');
    container.innerHTML = '';

    if (products.error) {
      container.innerHTML = `<p class="text-red-600">${products.error}</p>`;
      return;
    }

    products.forEach(product => {
      const imageElements = product.images.slice(0, 3).map(img =>
        `<img src="../uploads/${escapeHtml(img)}" class="w-full md:w-1/3 h-48 object-cover rounded-lg" />`
      ).join('');

    const productHtml = `
  <div class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all p-4 flex flex-col">
    <p class="text-gray-600 mb-3">${escapeHtml(product.product_description)}</p>
    
    <div class="flex gap-2 mb-4 h-32">
      ${
        product.images.length > 0
          ? product.images.slice(0, 3).map(img =>
              `<img src="../uploads/${escapeHtml(img)}" class="flex-1 object-cover rounded-md max-h-32 min-w-0" />`
            ).join('')
          : `<div class="flex-1 bg-gray-200 flex justify-center items-center rounded-md text-gray-500">No Image</div>`
      }
    </div>
<h2 class="text-xl font-semibold text-gray-800 mb-2">${escapeHtml(product.product_name)}</h2>
    <div class="mt-auto flex justify-between items-center">
    
      <span class="text-lg font-bold text-green-600">₱${Number(product.product_price).toFixed(2)}</span>
      <a href="government_order_product.php?product_id=${product.product_id}" 
         class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
         Order Now
      </a>
    </div>
  </div>
`;
      container.insertAdjacentHTML('beforeend', productHtml);
    });

  } catch (error) {
    document.getElementById('productContainer').innerHTML = `<p class="text-red-600">Error loading products.</p>`;
    console.error(error);
  }
}

function escapeHtml(text) {
  if (!text) return '';
  return text.replace(/[&<>"']/g, function (m) {
    return ({
      '&': '&amp;',
      '<': '&lt;',
      '>': '&gt;',
      '"': '&quot;',
      "'": '&#39;'
    })[m];
  });
}

// Trigger products on input changes (optional live filtering)
document.getElementById('searchQuery').addEventListener('input', fetchProducts);
document.getElementById('minPrice').addEventListener('input', fetchProducts);
document.getElementById('maxPrice').addEventListener('input', fetchProducts);

// Load products on page load
fetchProducts();
</script>
</body>
</html>
