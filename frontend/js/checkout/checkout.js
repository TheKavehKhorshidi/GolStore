// محصولات سبد خرید که از سرور گرفته می‌شن (فعلاً فقط برای نمایش خلاصه استفاده میشه)
let currentProducts = [];


// _______________________________ نمایش خلاصه سفارش _______________________________

async function renderCheckoutSummary() {
   const itemsContainer = document.getElementById('checkout-items');
   const products = await fetchCartProductsInfo();

   if (products.length === 0) {
      window.location.href = '/shop/frontend/php/cart/cart.php';
      return;
   }

   currentProducts = products;

   let total = 0;
   let itemsHtml = '';

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

cardNumberInput.addEventListener('input', () => {
   let value = cardNumberInput.value.replace(/\D/g, '').slice(0, 16);
   cardNumberInput.value = value.replace(/(.{4})/g, '$1-').replace(/-$/, '');
});

cardExpiryInput.addEventListener('input', () => {
   let value = cardExpiryInput.value.replace(/\D/g, '').slice(0, 4);
   if (value.length > 2) {
      value = value.slice(0, 2) + '/' + value.slice(2);
   }
   cardExpiryInput.value = value;
});

cardCvvInput.addEventListener('input', () => {
   cardCvvInput.value = cardCvvInput.value.replace(/\D/g, '').slice(0, 4);
});


// _______________________________ جمع‌آوری اطلاعات خریدار _______________________________

function getBuyerInfo() {
   return {
      name: document.getElementById('buyer-name').value.trim(),
      address: document.getElementById('buyer-address').value.trim(),
      phone: document.getElementById('buyer-phone').value.trim(),
      delivery_time: document.getElementById('delivery-time').value.trim(),
      notes: document.getElementById('order-notes').value.trim()
   };
}

// چک می‌کنه فیلدهای اجباری (نام، آدرس، تلفن) پر شده باشن
function validateBuyerInfo(buyer) {
   if (!buyer.name || !buyer.address || !buyer.phone) {
      alert('لطفاً نام، آدرس و شماره تماس رو وارد کنید');
      return false;
   }
   return true;
}


// _______________________________ مرحله ۱: زدن دکمه پرداخت → نمایش OTP _______________________________

document.getElementById('payment-form').addEventListener('submit', async (e) => {
   e.preventDefault();

   const buyer = getBuyerInfo();
   if (!validateBuyerInfo(buyer)) return;

   const payBtn = document.getElementById('pay-btn');
   const loadingEl = document.getElementById('payment-loading');

   payBtn.disabled = true;
   loadingEl.style.display = 'flex';

   // تاخیر مصنوعی، شبیه‌سازی اتصال اولیه به بانک برای ارسال کد
   await new Promise(resolve => setTimeout(resolve, 1500));

   loadingEl.style.display = 'none';
   payBtn.disabled = false;

   // فرم کارت رو مخفی و باکس OTP رو نشون می‌دیم
   document.getElementById('payment-form').style.display = 'none';
   document.getElementById('otp-box').style.display = 'block';
});


// _______________________________ مرحله ۲: تایید OTP → ارسال نهایی سفارش _______________________________

document.getElementById('confirm-otp-btn').addEventListener('click', async () => {
   const otpInput = document.getElementById('otp-input');
   const otpValue = otpInput.value.trim();

   if (otpValue.length !== 6) {
      alert('کد تایید ۶ رقمی رو کامل وارد کنید');
      return;
   }

   const confirmBtn = document.getElementById('confirm-otp-btn');
   const loadingEl = document.getElementById('payment-loading');

   confirmBtn.disabled = true;
   loadingEl.style.display = 'flex';

   // تاخیر مصنوعی، شبیه‌سازی تایید نهایی تراکنش
   await new Promise(resolve => setTimeout(resolve, 2000));

   const buyer = getBuyerInfo();

   try {
      const response = await fetch('/shop/frontend/php/checkout/checkout_process.php', {
         method: 'POST',
         headers: { 'Content-Type': 'application/json' },
         body: JSON.stringify({
            cart: getCart(),
            buyer: buyer
         })
      });

      const data = await response.json();

      if (!data.success) {
         alert(data.message || 'پرداخت ناموفق بود، دوباره تلاش کنید');
         confirmBtn.disabled = false;
         loadingEl.style.display = 'none';
         return;
      }

      clearCart();
      window.location.href = '/shop/frontend/php/checkout/receipt.php?order=' + data.order_number;

   } catch (err) {
      alert('خطایی رخ داد، دوباره تلاش کنید');
      confirmBtn.disabled = false;
      loadingEl.style.display = 'none';
   }
});


// نمایش اولیه خلاصه سفارش
renderCheckoutSummary();