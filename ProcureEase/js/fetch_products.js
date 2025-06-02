document.addEventListener('DOMContentLoaded', () => {
  const productList = document.getElementById('productList');

  // Modal elements
  const modal = document.getElementById('productModal');
  const closeModal = document.getElementById('closeModal');
  const modalName = document.getElementById('modalName');
  const modalDescription = document.getElementById('modalDescription');
  const modalPrice = document.getElementById('modalPrice');
  const modalQuantity = document.getElementById('modalQuantity');
  const swiperWrapper = document.getElementById('modalSwiperWrapper');
  // Initialize global swiper variable
  window.modalSwiper = null;

  fetch('../supplier/fetch_products.php')
    .then(response => response.json())
    .then(products => {
      productList.innerHTML = '';

      if (products.length === 0) {
        productList.innerHTML = '<p class="text-gray-600">No products found.</p>';
        return;
      }

      products.forEach(product => {
        const card = document.createElement('div');
        card.className = 'bg-white p-2 w-48 h-56 rounded-lg shadow hover:shadow-lg transition flex flex-col';

        const imageHTML = product.images.length
          ? `<img src="${product.images[0]}" alt="${product.product_name}" class="w-48 h-32 object-cover rounded self-center">`
          : '';

        card.innerHTML = `
          ${imageHTML}
          <div class="mt-2">
            <h3 class="text-md font-semibold truncate">${product.product_name}</h3>
            <p class="text-amber-500 font-bold">₱${product.product_price}</p>
            <button 
              class="bg-amber-500 hover:bg-amber-600 text-white px-3 py-1 rounded text-sm w-full view-btn" 
              data-product='${JSON.stringify(product)}'>
              View
            </button>
          </div>
        `;

        productList.appendChild(card);
      });

      // Modal open handler
      document.querySelectorAll('.view-btn').forEach(button => {
        button.addEventListener('click', () => {
          const product = JSON.parse(button.getAttribute('data-product'));

          // Clear old slides
          swiperWrapper.innerHTML = '';

          // Add new slides
          if (product.images.length) {
            product.images.forEach(img => {
              const slide = document.createElement('div');
              slide.className = 'swiper-slide';
              slide.innerHTML = `<img src="${img}" class="max-w-[500px] max-h-[300px] object-contain mx-auto rounded">`;
              swiperWrapper.appendChild(slide);
            });
          } else {
            const slide = document.createElement('div');
            slide.className = 'swiper-slide';
            slide.innerHTML = `<div class="w-full h-full flex items-center justify-center text-gray-400">No image</div>`;
            swiperWrapper.appendChild(slide);
          }

          // Reinitialize Swiper after DOM updates
          if (window.modalSwiper && typeof window.modalSwiper.destroy === 'function') {
            window.modalSwiper.destroy(true, true);
          }

          // Wait for DOM update before initializing Swiper
          requestAnimationFrame(() => {
            const actualSlideCount = swiperWrapper.querySelectorAll('.swiper-slide').length;
            const enableLoop = actualSlideCount >= 3;

            window.modalSwiper = new Swiper('#modalSwiper', {
              loop: enableLoop,
              pagination: { el: '.swiper-pagination' },
              navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev'
              }
            });
          });
          window.currentProductId = product.product_id;

          modalName.value = product.product_name;
          modalDescription.value = product.product_description;
          modalPrice.value = product.product_price;
          modalQuantity.value = product.product_quantity;
    
          modal.classList.remove('hidden');
          
        });
      });
    })
    .catch(err => {
      console.error('Error fetching products:', err);
      productList.innerHTML = '<p class="text-red-500">Failed to load products.</p>';
    });

  // Close modal
  closeModal.addEventListener('click', () => {
    modal.classList.add('hidden');
  });

  window.addEventListener('click', (e) => {
    if (e.target === modal) {
      modal.classList.add('hidden');
    }
  });


});
