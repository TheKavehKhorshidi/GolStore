let currentProducts = []; // محصولات سبد خرید که از سرور گرفته می‌شن، برای استفاده در submit

// ___________________________________ نمایش خلاصه سفارش ___________________________________
async function renderCheckoutSummary() {
   const itemsEl = document.getElementById('checkout-items');
   const products = await fetchCartProductsInfo();

   // اگر سبد خرید خالی بود، برگرد به صفحه سبد خرید
   if (products.length === 0) {
      window.location.href = '/shop/frontend/php/cart/cart.php';
      return;
   }

   currentProducts = products;

   let total = 0;

   itemsEl.innerHTML = products.map(product => {
      const lineTotal = product.price * product.quantity;
      total += lineTotal;

      return `
         <div class="checkout-summary-item">
            <div>
               <div class="item-name">${product.name}</div>
               <div class="item-qty">تعداد: ${product.quantity}</div>
            </div>
            <div class="item-price">${lineTotal.toLocaleString('fa-IR')} تومان</div>
         </div>
      `;
   }).join('');

   document.getElementById('cart-total-price').textContent =
      total.toLocaleString('fa-IR') + ' تومان';
}
// ___________________________________________________________________________________________________________________




// ___________________________________ فرمت خودکار فیلدهای کارت (فقط ظاهری) ___________________________________
const cardNumberInput = document.getElementById('card-number');
const cardExpiryInput = document.getElementById('card-expiry');
const cardCvvInput = document.getElementById('card-cvv');

// اجازه فقط عدد و گذاشتن خط‌تیره هر ۴ رقم
cardNumberInput.addEventListener('input', () => {
   let value = cardNumberInput.value.replace(/\D/g, '').slice(0, 16);
   cardNumberInput.value = value.replace(/(.{4})/g, '$1-').replace(/-$/, '');
});

// فرمت MM/YY
cardExpiryInput.addEventListener('input', () => {
   let value = cardExpiryInput.value.replace(/\D/g, '').slice(0, 4);
   if (value.length > 2) {
      value = value.slice(0, 2) + '/' + value.slice(2);
   }
   cardExpiryInput.value = value;
});

// فقط عدد برای CVV2
cardCvvInput.addEventListener('input', () => {
   cardCvvInput.value = cardCvvInput.value.replace(/\D/g, '').slice(0, 4);
});
// ___________________________________________________________________________________________________________________




// ___________________________________ ارسال پرداخت ___________________________________
document.getElementById('payment-form').addEventListener('submit', async (e) => {
   e.preventDefault();

   const payBtn = document.getElementById('pay-btn');
   const loadingEl = document.getElementById('payment-loading');

   payBtn.disabled = true;
   loadingEl.style.display = 'flex';

   // تاخیر مصنوعی برای شبیه‌سازی اتصال به بانک
   await new Promise(resolve => setTimeout(resolve, 2000));

   try {
      const response = await fetch('/shop/frontend/php/checkout/checkout_process.php', {
         method: 'POST',
         headers: { 'Content-Type': 'application/json' },
         body: JSON.stringify({ cart: getCart() })
      });

      const data = await response.json();

      if (!data.success) {
         alert(data.message || 'پرداخت ناموفق بود، دوباره تلاش کنید');
         payBtn.disabled = false;
         loadingEl.style.display = 'none';
         return;
      }

      // سبد خرید رو خالی کن چون سفارش ثبت شد
      clearCart();

      // برو به صفحه رسید با شماره سفارش
      window.location.href = '/shop/frontend/php/checkout/receipt.php?order=' + data.order_number;

   } catch (err) {
      alert('خطایی رخ داد، دوباره تلاش کنید');
      payBtn.disabled = false;
      loadingEl.style.display = 'none';
   }
});
// ___________________________________________________________________________________________________________________




// نمایش اولیه خلاصه سفارش
renderCheckoutSummary();