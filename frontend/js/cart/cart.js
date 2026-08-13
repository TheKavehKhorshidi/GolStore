// frontend/js/cart.js
// مدیریت سبد خرید: ذخیره در localStorage + گرفتن اطلاعات محصولات از API

const CART_KEY = 'shop_cart';
const API_URL = '/shop/frontend/php/cart/cart_get.php';

// ---------- توابع پایه‌ی localStorage ----------

// خواندن سبد خرید فعلی: خروجی آرایه‌ای از { id, quantity }
function getCart() {
   const raw = localStorage.getItem(CART_KEY);
   return raw ? JSON.parse(raw) : [];
}

// ذخیره‌ی سبد خرید در localStorage
function saveCart(cart) {
   localStorage.setItem(CART_KEY, JSON.stringify(cart));
}

// افزودن محصول به سبد (اگه از قبل بود، تعدادش زیاد می‌شه)
function addToCart(productId, quantity = 1) {
   const cart = getCart();
   const existing = cart.find(item => item.id === productId);

   if (existing) {
      existing.quantity += quantity;
   } else {
      cart.push({ id: productId, quantity });
   }

   saveCart(cart);
   updateCartBadge();
   showCartToast('به سبد خرید اضافه شد');
}

// نمایش یه پیام کوتاه بالای صفحه بعد از افزودن/حذف محصول
function showCartToast(message) {
   let toast = document.getElementById('cart-toast');

   // اگه toast قبلاً ساخته نشده، یه بار می‌سازیمش
   if (!toast) {
      toast = document.createElement('div');
      toast.id = 'cart-toast';
      toast.style.cssText = `
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%) translateY(-20px);
            background: #4f6d43;
            color: #fff;
            padding: 12px 24px;
            border-radius: 999px;
            font-size: 14px;
            font-family: inherit;
            box-shadow: 0 4px 16px rgba(0,0,0,0.15);
            z-index: 9999;
            opacity: 0;
            transition: opacity 0.25s ease, transform 0.25s ease;
            pointer-events: none;
        `;
      document.body.appendChild(toast);
   }

   toast.textContent = message;
   toast.style.opacity = '1';
   toast.style.transform = 'translateX(-50%) translateY(0)';

   clearTimeout(toast._hideTimer);
   toast._hideTimer = setTimeout(() => {
      toast.style.opacity = '0';
      toast.style.transform = 'translateX(-50%) translateY(-20px)';
   }, 1800);
}

// تغییر تعداد یک آیتم (اگه صفر یا کمتر بشه، حذف می‌شه)
function updateQuantity(productId, newQuantity) {
   let cart = getCart();

   if (newQuantity <= 0) {
      cart = cart.filter(item => item.id !== productId);
   } else {
      const item = cart.find(item => item.id === productId);
      if (item) item.quantity = newQuantity;
   }

   saveCart(cart);
   updateCartBadge();
}

// حذف کامل یک آیتم از سبد
function removeFromCart(productId) {
   let cart = getCart().filter(item => item.id !== productId);
   saveCart(cart);
   updateCartBadge();
}

// خالی کردن کل سبد (بعد از پرداخت موفق کاربردی داره)
function clearCart() {
   localStorage.removeItem(CART_KEY);
   updateCartBadge();
}

// تعداد کل آیتم‌ها (برای نمایش عدد کنار آیکون سبد خرید در همه صفحات)
function getCartItemCount() {
   return getCart().reduce((sum, item) => sum + item.quantity, 0);
}

// آپدیت کردن badge عددی سبد خرید در navbar (اگه عنصری با id="cart-count" وجود داشته باشه)
function updateCartBadge() {
   const badge = document.getElementById('cart-count');
   if (badge) badge.textContent = getCartItemCount();
}

// ---------- ارتباط با بک‌اند برای گرفتن اطلاعات به‌روز محصولات ----------

// گرفتن اطلاعات کامل (اسم، قیمت، تصویر) محصولات داخل سبد از API
async function fetchCartProductsInfo() {
   const cart = getCart();
   if (cart.length === 0) return [];

   const ids = cart.map(item => item.id);

   const response = await fetch(API_URL, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ ids })
   });

   const data = await response.json();
   if (!data.success) return [];

   // ترکیب اطلاعات محصول (از دیتابیس) با تعداد (از localStorage)
   return data.products.map(product => {
      const cartItem = cart.find(item => item.id === product.id);
      return { ...product, quantity: cartItem ? cartItem.quantity : 1 };
   });
}

// این تابع در هر صفحه‌ای که لود می‌شه، badge سبد رو آپدیت می‌کنه
document.addEventListener('DOMContentLoaded', updateCartBadge);