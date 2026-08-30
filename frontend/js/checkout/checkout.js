// محصولات سبد خرید که از سرور گرفته می‌شن (فعلاً فقط برای نمایش خلاصه استفاده میشه)
let currentProducts = [];


// _______________________________ نمایش خلاصه سفارش _______________________________

// نمایش لیست محصولات و جمع کل قیمت، بالای فرم پرداخت
async function renderCheckoutSummary() {
   const itemsContainer = document.getElementById('checkout-items');
   const products = await fetchCartProductsInfo();

   // اگر سبد خرید خالی بود، برگرد به صفحه سبد خرید
   if (products.length === 0) {
      window.location.href = '/shop/frontend/php/cart/cart.php';
      return;
   }

   currentProducts = products;

   let total = 0;
   let itemsHtml = '';

   // برای هر محصول، یه بلوک HTML میسازیم و به جمع کل هم اضافه می‌کنیم
   for (const product of products) {
      const lineTotal = product.price * product.quantity;
      total += lineTotal;

      itemsHtml += `
         <div class="checkout-summary-item">
            <div>
               <div class="item-name">${product.name}</div>
               <div class="item-qty">تعداد: ${product.quantity}</div>
            </div>
            <div class="item-price">${lineTotal.toLocaleString('fa-IR')} تومان</div>
         </div>
      `;
   }

   itemsContainer.innerHTML = itemsHtml;
   document.getElementById('cart-total-price').textContent = total.toLocaleString('fa-IR') + ' تومان';
}


// _______________________________ فرمت خودکار فیلدهای کارت (فقط ظاهری) _______________________________

const cardNumberInput = document.getElementById('card-number');
const cardExpiryInput = document.getElementById('card-expiry');
const cardCvvInput = document.getElementById('card-cvv');

// شماره کارت: فقط عدد، و گذاشتن خط‌تیره هر ۴ رقم
cardNumberInput.addEventListener('input', () => {
   let value = cardNumberInput.value.replace(/\D/g, '').slice(0, 16);
   cardNumberInput.value = value.replace(/(.{4})/g, '$1-').replace(/-$/, '');
});

// تاریخ انقضا: فرمت MM/YY
cardExpiryInput.addEventListener('input', () => {
   let value = cardExpiryInput.value.replace(/\D/g, '').slice(0, 4);

   if (value.length > 2) {
      value = value.slice(0, 2) + '/' + value.slice(2);
   }

   cardExpiryInput.value = value;
});

// CVV2: فقط عدد
cardCvvInput.addEventListener('input', () => {
   cardCvvInput.value = cardCvvInput.value.replace(/\D/g, '').slice(0, 4);
});


// _______________________________ ارسال پرداخت _______________________________

document.getElementById('payment-form').addEventListener('submit', async (e) => {
   e.preventDefault();

   const payBtn = document.getElementById('pay-btn');
   const loadingEl = document.getElementById('payment-loading');

   // دکمه رو غیرفعال می‌کنیم و لودینگ رو نشون می‌دیم تا کاربر دوباره کلیک نکنه
   payBtn.disabled = true;
   loadingEl.style.display = 'flex';

   // یه تاخیر مصنوعی ۲ ثانیه‌ای، فقط برای شبیه‌سازی اتصال به بانک
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

      // پرداخت موفق بود، پس سبد خرید رو خالی می‌کنیم
      clearCart();

      // و کاربر رو به صفحه رسید، همراه با شماره سفارش، هدایت می‌کنیم
      window.location.href = '/shop/frontend/php/checkout/receipt.php?order=' + data.order_number;

   } catch (err) {
      alert('خطایی رخ داد، دوباره تلاش کنید');
      payBtn.disabled = false;
      loadingEl.style.display = 'none';
   }
});


// نمایش اولیه خلاصه سفارش، به محض لود شدن اسکریپت
renderCheckoutSummary();