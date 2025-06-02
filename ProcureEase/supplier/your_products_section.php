<link
  rel="stylesheet"
  href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"
/>
<!-- Swiper JS -->
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

<style>
  /* Make navigation buttons smaller */
.swiper-button-next,
.swiper-button-prev {
  width: 50px;
  height: 50px;
  font-size: 12px;
  background-color: rgba(0, 0, 0, 0.4); /* Optional: add a background for visibility */
  border-radius: 9999px;
  padding: 5px;
}

/* Optional: Change icon size inside buttons */
.swiper-button-next::after,
.swiper-button-prev::after {
  font-size: 14px;
}

/* Make pagination bullets smaller */
.swiper-pagination-bullet {
  width: 6px;
  height: 6px;
}

</style>

<h2 class="text-3xl font-bold mb-6 text-amber-500">Your Posted Products</h2>
<div id="productList" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-2">
    
  <!-- Products will be inserted here dynamically -->
  
</div>
<script src="../js/fetch_products.js"></script>
 <!-- Modal for product details -->
<div id="productModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
  <div class="bg-white w-full max-w-3xl p-6 rounded-lg shadow-lg relative">
    <button id="closeModal" class="absolute top-2 right-2 text-gray-500 hover:text-gray-700 text-2xl">&times;</button>
    
    <!-- Swiper carousel inside the modal -->
    <div class="swiper w-full h-40 md:h-40 lg:h-40 mb-4 rounded" id="modalSwiper">
      <div class="swiper-wrapper" id="modalSwiperWrapper">
        <!-- Images will be injected here -->
      </div>

      <!-- Swiper navigation -->
      <div class="swiper-button-next"></div>
      <div class="swiper-button-prev"></div>
      <div class="swiper-pagination"></div>
    </div>

    <form id="editProductForm" class="space-y-3">
  <div>
    <label class="block text-sm font-medium text-gray-700">Product Name</label>
    <input id="modalName" type="text" class="w-full border border-gray-300 rounded px-3 py-2" />
  </div>
  <div>
    <label class="block text-sm font-medium text-gray-700">Description</label>
    <textarea id="modalDescription" class="w-full border border-gray-300 rounded px-3 py-2" rows="3"></textarea>
  </div>
  <div>
    <label class="block text-sm font-medium text-gray-700">Price</label>
    <input id="modalPrice" type="number" step="0.01" class="w-full border border-gray-300 rounded px-3 py-2" />
  </div>
  <div>
    <label class="block text-sm font-medium text-gray-700">Quantity</label>
    <input id="modalQuantity" type="number" class="w-full border border-gray-300 rounded px-3 py-2" />
  </div>

  <div class="text-right">
    <button type="submit" class="bg-amber-500 hover:bg-amber-600 text-white px-4 py-2 rounded">
      Save Changes
    </button>
  </div>
</form>
</div>

