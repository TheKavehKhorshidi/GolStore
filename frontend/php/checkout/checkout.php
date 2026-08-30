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

            <!-- این دو تا فیلد فقط نمایشی هستن، از سشن کاربر پر میشن و قابل ویرایش نیستن -->
            <div class="checkout-field">
               <label>نام</label>
               <input type="text" value="<?php echo htmlspecialchars($_SESSION['user_name']); ?>" disabled>
            </div>
            <div class="checkout-field">
               <label>ایمیل</label>
               <input type="text" value="<?php echo htmlspecialchars($_SESSION['user_email']); ?>" disabled>
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