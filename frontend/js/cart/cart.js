// مدیریت سبد خرید با لوکال استوریج و ای چی ای
const CART_KEY = 'shop_cart';
const API_URL = '/shop/frontend/php/cart/cart_get.php';




//  ------------------------- localStorage -------------------------



// دریافت سبد خرید
function getCart() {
   const cart = localStorage.getItem(CART_KEY);
   return cart ? JSON.parse(cart) : [];
}

// ذخیره محصول در سبد خرید - ذخیره در لوکال استوریج
function saveCart(cart) {
   localStorage.setItem(CART_KEY, JSON.stringify(cart));
}

// افزودن محصول به سبد
function addToCart(productId, quantity = 1) {
   const cart = getCart(); // ایدی کارت ها رو از لوکال استوریج بیار
   const existingItem = cart.find(item => item.id === productId); // برسی وجود محصول

   if (existingItem) {
      existingItem.quantity += quantity; // اگر محصول وجود داشت یدونه به شمارنده اضافه کن
   } else {
      cart.push({ id: productId, quantity }); // اگر محصول وجود نداشت محصول به سبد خرید اضافه کن
   }

   saveCart(cart); //فانکشن اپدیت کردن لوکال استوریج
   updateCartBadge();  //فانکشن اپدیت کردن شمارنده سفارش ها یا همون بدج
   showCartToast('به سبد خرید اضافه شد'); // فانکشن  پیام اضافه شدن محصول به سبد خرید
}

// نمایش پیام سبد خرید
function showCartToast(message) {
   let toast = document.getElementById('cart-toast');

   if (!toast) { // اگرتوست وجود نداشت
      toast = document.createElement('div'); // یه دیو بساز
      toast.id = 'cart-toast'; // ایدیش این باشه

      // استایلش این باشه
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
         box-shadow: 0 4px 16px rgba(0, 0, 0, 0.15);
         z-index: 9999;
         opacity: 0;
         transition: opacity 0.25s ease, transform 0.25s ease;
         pointer-events: none;
      `;

      document.body.appendChild(toast); // به یادی اضافه بشه
   }

   toast.textContent = message; // متنش این باشه
   toast.style.opacity = '1';
   toast.style.transform = 'translateX(-50%) translateY(0)';

   clearTimeout(toast._hideTimer); // پاک کردن تایر

   toast._hideTimer = setTimeout(() => { // تایمر بعد 1800 میلی سانیه خود کار استایل زیر رو بده
      toast.style.opacity = '0';
      toast.style.transform = 'translateX(-50%) translateY(-20px)';
   }, 1800);
}

// تغییر تعداد محصول
function updateQuantity(productId, newQuantity) {
   let cart = getCart(); // گرفتن کل محصولات

   if (newQuantity <= 0) { // اگر شمارنده برابر یا کمتر از صغر بشه
      cart = cart.filter(item => item.id !== productId); // کلا از سبد خرید حرف میشه
   } else { // در غیر این صورت
      const item = cart.find(item => item.id === productId); // ایا ایتم وجود دارد

      if (item) { //  اگر وجود داشت تهداد محصول برابر مقدار جدید میشه
         item.quantity = newQuantity; 
      }
   }

   saveCart(cart);
   updateCartBadge();
}

// حذف محصول از سبد
function removeFromCart(productId) {
   const cart = getCart().filter(item => item.id !== productId); // قیلتر کن همه رو به جز ایدی یه بهت میدم

   saveCart(cart); //اپدیت کار ها
   updateCartBadge(); // اپدیت تعداد محصولات یا مون بدج
}

// خالی کردن سبد
// برای وفتی که خرید موفقیت امیز بود
function clearCart() {
   localStorage.removeItem(CART_KEY);
   updateCartBadge();
}




// ___________________________________ بخش  تعداد محصولات - عدد کنار ایکون سبد خرید ___________________________________
// دریافت تعداد کل محصولات
function getCartItemCount() {
   return getCart().reduce((sum, item) => sum + item.quantity, 0);
}

// آپدیت عدد کنار آیکون سبد یا همون بدج
function updateCartBadge() {
   const badge = document.getElementById('cart-count');

   if (badge) {
      badge.textContent = getCartItemCount();
   }
}
// ___________________________________________________________________________________________________________________




// دریافت اطلاعات محصولات از API
async function fetchCartProductsInfo() {
   const cart = getCart();

   if (cart.length === 0) {
      return [];
   }

   const ids = cart.map(item => item.id);

   const response = await fetch(API_URL, {
      method: 'POST',
      headers: {
         'Content-Type': 'application/json'
      },
      body: JSON.stringify({ ids })
   });

   const data = await response.json();

   if (!data.success) {
      return [];
   }

   // ترکیب اطلاعات محصول با تعداد موجود در سبد
   return data.products.map(product => {
      const cartItem = cart.find(item => item.id === product.id);

      return {
         ...product,
         quantity: cartItem ? cartItem.quantity : 1
      };
   });
}

// آپدیت سبد بعد از لود صفحه
document.addEventListener('DOMContentLoaded', updateCartBadge);