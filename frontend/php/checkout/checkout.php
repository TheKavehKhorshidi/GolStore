<?php
require '../../partials/config.php';
session_start();

// اگر کاربر لاگین نکرده بود، بفرستش صفحه ورود
if (!isset($_SESSION['user_id'])) {
   header('Location: ' . $baseUrl . 'php/auth/login.php');
   exit;
}
?>

<!DOCTYPE html>
<html lang="fa" dir="rtl">

<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">

   <title>تکمیل خرید</title>

   <link rel="stylesheet" href="/shop/frontend/css/style.css">
   <link rel="stylesheet" href="/shop/frontend/css/cart/cart.css">
   <link rel="stylesheet" href="/shop/frontend/css/checkout/checkout.css">
   <link rel="stylesheet" href="/shop/frontend/css/header/header.css">
</head>

<body>

   <?php include '../../partials/header/header.php'; ?>

   <main class="container cart-page">

      <div class="cart-header">
         <h1 class="section-title">تکمیل خرید</h1>
         <p class="cart-subtitle">اطلاعات پرداخت رو وارد کن</p>
      </div>

      <div class="cart-layout">

         <!-- فرم پرداخت -->
         <div class="checkout-form-box">

            <h2>اطلاعات خریدار</h2>


            <div class="checkout-field">
               <label>نام و نام خانوادگی *</label>
               <input type="text" id="buyer-name" required
                  value="<?php echo htmlspecialchars($_SESSION['user_name']); ?>">
            </div>
            <div class="checkout-field">
               <label>آدرس (خیابان، پلاک، واحد) *</label>
               <input type="text" id="buyer-address" placeholder="آدرس خیابان" required>
            </div>
            <div class="checkout-field">
               <label>تلفن همراه *</label>
               <input type="text" id="buyer-phone" placeholder="09xxxxxxxxx" required>
            </div>
            <div class="checkout-field">
               <label>زمان دقیق تحویل سفارش</label>
               <input type="text" id="delivery-time" placeholder="مثلاً: شنبه ۲۵ بهمن - ساعت ۱۷ تا ۱۹">
            </div>
            <div class="checkout-field">
               <label>توضیحات تکمیلی سفارش</label>
               <textarea id="order-notes" placeholder="توضیحات تکمیلی سفارش رو اینجا بنویسید."></textarea>
            </div>

            <h2>اطلاعات کارت (نمایشی)</h2>

            <!-- این فرم فقط شبیه‌سازیه، اطلاعات کارت واقعی به هیچ بانکی ارسال نمیشه -->
            <form id="payment-form">
               <div class="checkout-field">
                  <label>شماره کارت</label>
                  <input type="text" id="card-number" maxlength="19" placeholder="XXXX-XXXX-XXXX-XXXX" required>
               </div>

               <div class="checkout-row">
                  <div class="checkout-field">
                     <label>تاریخ انقضا</label>
                     <input type="text" id="card-expiry" maxlength="5" placeholder="MM/YY" required>
                  </div>
                  <div class="checkout-field">
                     <label>CVV2</label>
                     <input type="text" id="card-cvv" maxlength="4" placeholder="___" required>
                  </div>
               </div>

               <button type="submit" id="pay-btn" class="btn-primary">پرداخت</button>
            </form>
            
            <!-- باکس رمز پویا - اول مخفیه، بعد از زدن دکمه پرداخت نشون داده میشه -->
            <div id="otp-box" class="otp-box" style="display:none;">
               <h3>کد تایید پویا</h3>
               <p>کد ۶ رقمی ارسال‌شده به شماره همراه خود را وارد کنید (شبیه‌سازی‌شده)</p>
               <input type="text" id="otp-input" maxlength="6" placeholder="------">
               <button type="button" id="confirm-otp-btn" class="btn-primary">تایید و پرداخت</button>
            </div>

         </div>

         <!-- خلاصه سفارش - محتواش با جاوااسکریپت پر میشه -->
         <aside id="cart-summary" class="cart-summary">
            <h2>خلاصه سفارش</h2>
            <div id="checkout-items"></div>
            <div class="cart-summary-row">
               <span>جمع کل</span>
               <strong id="cart-total-price">۰ تومان</strong>
            </div>
         </aside>

      </div>

   </main>

   <!-- حالت لودینگ، وقتی کاربر دکمه پرداخت رو میزنه نشون داده میشه -->
   <div id="payment-loading" class="payment-loading" style="display:none;">
      <div class="spinner"></div>
      <span>در حال اتصال به بانک...</span>
   </div>

   <script src="/shop/frontend/js/cart/cart.js"></script>
   <script src="/shop/frontend/js/checkout/checkout.js"></script>

</body>

</html>