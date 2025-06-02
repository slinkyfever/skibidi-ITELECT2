document.addEventListener('DOMContentLoaded', () => {
    loadProducts();
});

function loadProducts() {
    fetch('../supplier/fetch_products.php')
    .then(response => response.json())
    .then(products => {
        const productList = document.getElementById('availableProducts');
        productList.innerHTML = '';

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
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Something went wrong!');
    });
}


loadProducts();