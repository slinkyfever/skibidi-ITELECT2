document.addEventListener('DOMContentLoaded', () => {
    loadProducts(); // Ensure this is called after the page has loaded

    const productForm = document.getElementById('productForm');
    if (productForm) {
        productForm.addEventListener('submit', function (e) {
            e.preventDefault();
            submitProduct();
        });
    }

    // Ensure elements exist before assigning event listeners
    const imageInput = document.getElementById('imageInput');
    const imagePreview = document.getElementById('imagePreview');
    const imageError = document.getElementById('imageError');

    if (imageInput && imagePreview && imageError) {
        imageInput.addEventListener('change', () => {
            // Clear the previous previews
            imagePreview.innerHTML = ''; 

            const files = Array.from(imageInput.files);
            const limitedFiles = files.slice(0, 3); // Take only the first 3 files

            // Check if at least 3 images are selected
            if (limitedFiles.length < 3) {
                imageError.classList.remove('hidden');
            } else {
                imageError.classList.add('hidden');
            }

            // Preview the selected images
            limitedFiles.forEach(file => {
                if (file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        const img = document.createElement('img');
                        img.src = e.target.result;
                        img.className = "w-24 h-24 object-cover rounded shadow";
                        imagePreview.appendChild(img);
                    };
                    reader.readAsDataURL(file);
                }
            });
        });
    }
});

function loadProducts() {
    fetch('../supplier/fetch_products.php')
    .then(response => response.json())
    .then(products => {
        const productList = document.getElementById('availableProducts');
        if (productList) {
            productList.innerHTML = ''; // Clear the product list first

            products.forEach(product => {
                const productCard = document.createElement('div');
                productCard.classList.add('bg-white', 'p-4', 'rounded', 'shadow-lg');

                let imagesHtml = '';
                if (product.images && product.images.length > 0) {
                    product.images.forEach(image => {
                        imagesHtml += `<img src="${image}" alt="Product image" class="w-full h-auto mb-2">`;
                    });
                }

                productCard.innerHTML = `
                    <h4 class="text-xl font-semibold">${product.product_name}</h4>
                    <p class="text-gray-700">${product.product_description}</p>
                    <p class="font-bold text-amber-500">$${product.product_price}</p>
                    ${imagesHtml}
                `;
                productList.appendChild(productCard);
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Something went wrong!');
    });
}

function submitProduct() {
    const form = document.getElementById('productForm');
    const formData = new FormData(form);
    const imageCount = formData.getAll('product_images[]').length;

    // Validate image count
    if (imageCount < 3) {
        alert('Please upload at least 3 product images.');
        return;
    } else if (imageCount > 3) {
        alert('You can upload a maximum of 3 images.');
        return;
    }

   fetch('../supplier/submit_product.php', {
    method: 'POST',
    body: formData
})
.then(response => response.text())
.then(data => {
    if (data.trim() === 'success') {
        alert('Product posted successfully!');
        loadProducts(); // Reload product list
        form.reset();

        const imagePreview = document.getElementById('imagePreview');
        if (imagePreview) {
            imagePreview.innerHTML = '';
        }
    } else if (data.trim() === 'limit_reached') {
        alert('You’ve reached your limit of 5 products. Subscribe now to unlock full access.');
    } else {
        console.log('Error: ' + data);
    }
})
.catch(error => {
    console.error('Error:', error);
    alert('Something went wrong!');
});
}
