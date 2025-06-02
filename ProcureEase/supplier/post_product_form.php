<h2 class="text-3xl font-bold text-amber-500 mb-6">Post a new Product</h2>
<div class="bg-white p-6 rounded-lg shadow">
    <form id="productForm" class="space-y-6" enctype="multipart/form-data">
        <div>
            <label class="block text-gray-700 font-semibold">Title</label>
            <input type="text" name="product_name" class="w-full p-3 border border-gray-300 rounded" required>
        </div>
        <div>
            <label class="block text-gray-700 font-semibold">Description</label>
            <textarea name="product_description" class="w-full p-3 border border-gray-300 rounded" rows="4" required></textarea>
        </div>
        <div>
            <label class="block text-gray-700 font-semibold">Price</label>
            <input type="number" name="product_price" class="w-full p-3 border border-gray-300 rounded" required>
        </div>
        <div>
            <label class="block text-gray-700 font-semibold">Quantity</label>
            <input type="number" name="product_quantity" class="w-full p-3 border border-gray-300 rounded" required min="1">
        </div>

        <div>
            <label class="block text-gray-700 font-semibold">Product Photos (Min. 3)</label>

            <!-- Hidden file input -->
            <input type="file" name="product_images[]" multiple accept="image/*" class="hidden" id="imageInput" required>

            <!-- Custom icon button -->
            <label for="imageInput" class="inline-flex items-center gap-2 px-4 py-2 bg-amber-500 text-white rounded cursor-pointer hover:bg-amber-600 transition w-fit">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                </svg>

                <span>Select Photos</span>
            </label>

            <div id="imagePreview" class="mt-4 flex gap-2 flex-wrap"></div>
            <p id="imageError" class="text-red-500 text-sm mt-2 hidden">Please upload at least 3 images.</p>
        </div>
        <button type="submit" class="w-32 bg-amber-500 text-white p-3 rounded hover:bg-amber-600 transition">Post Product</button>
    </form>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>