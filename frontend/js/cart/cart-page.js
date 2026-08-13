
async function renderCartPage() {
   const loadingEl = document.getElementById('cart-loading');
   const emptyEl = document.getElementById('cart-empty');
   const itemsEl = document.getElementById('cart-items');
   const summaryEl = document.getElementById('cart-summary');

   const products = await fetchCartProductsInfo();

   loadingEl.style.display = 'none';

   // نمایش سبد خالی
   if (products.length === 0) {
      emptyEl.style.display = 'flex';
      summaryEl.style.display = 'none';
      return;
   }

   emptyEl.style.display = 'none';
   itemsEl.style.display = 'flex';

   let total = 0;

   itemsEl.innerHTML = products.map(product => {
      const lineTotal = product.price * product.quantity;

      total += lineTotal;

      return `
         <div class="cart-item" data-id="${product.id}">

            <img
               src="/shop/frontend/${product.image}"
               alt="${product.name}"
               class="cart-item-img"
            >

            <div class="cart-item-info">
               <h3>${product.name}</h3>

               <p class="cart-item-price">
                  ${product.price.toLocaleString('fa-IR')} تومان
               </p>
            </div>

            <div class="cart-item-qty">

               <button
                  class="qty-btn"
                  data-action="decrease"
                  data-id="${product.id}"
                  type="button"
               >
                  −
               </button>

               <span>${product.quantity}</span>

               <button
                  class="qty-btn"
                  data-action="increase"
                  data-id="${product.id}"
                  type="button"
               >
                  +
               </button>

            </div>

            <div class="cart-item-total">
               ${lineTotal.toLocaleString('fa-IR')} تومان
            </div>

            <button
               class="remove-btn"
               data-id="${product.id}"
               title="حذف از سبد"
               type="button"
            >
               <svg
                  width="18"
                  height="18"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="2"
               >
                  <path d="M3 6h18M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2m3 0v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6h14z" />
               </svg>
            </button>

         </div>
      `;
   }).join('');

   // نمایش قیمت کل
   document.getElementById('cart-total-price').textContent =
      total.toLocaleString('fa-IR') + ' تومان';

   summaryEl.style.display = 'block';

   // دکمه‌های افزایش و کاهش تعداد
   itemsEl.querySelectorAll('.qty-btn').forEach(button => {
      button.addEventListener('click', () => {
         const id = parseInt(button.dataset.id);
         const cart = getCart();
         const item = cart.find(item => item.id === id);

         if (!item) {
            return;
         }

         const newQuantity =
            button.dataset.action === 'increase'
               ? item.quantity + 1
               : item.quantity - 1;

         updateQuantity(id, newQuantity);
         renderCartPage();
      });
   });

   // دکمه‌های حذف محصول
   itemsEl.querySelectorAll('.remove-btn').forEach(button => {
      button.addEventListener('click', () => {
         const id = parseInt(button.dataset.id);

         removeFromCart(id);
         renderCartPage();
      });
   });
}

// نمایش اولیه سبد خرید
renderCartPage();