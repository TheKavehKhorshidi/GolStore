<?php
require '../../partials/config.php';
?>

<!DOCTYPE html>
<html lang="fa" dir="rtl">

<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">

   <title>سبد خرید</title>

   <link rel="stylesheet" href="/shop/frontend/css/style.css">
   <link rel="stylesheet" href="/shop/frontend/css/cart/cart.css">
   <link rel="stylesheet" href="/shop/frontend/css/header/header.css">
</head>

<body>

   <!-- header -->
   <?php include '../../partials/header/header.php'; ?>


   
   <main class="container cart-page">

      <!-- header سبد خرید -->
      <div class="cart-header">
         <h1 class="section-title">سبد خرید</h1>

         <p class="cart-subtitle">
            محصولاتی که برای خرید انتخاب کردی
         </p>
      </div>

      <!-- loading -->
      <div id="cart-loading" class="cart-state">
         <div class="spinner"></div>
         <span>در حال بارگذاری...</span>
      </div>

      <!-- سبد خرید خالی -->
      <div id="cart-empty" class="cart-state" style="display: none;">

         <div class="svgCon">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
               stroke="currentColor">
               <path stroke-linecap="round" stroke-linejoin="round"
                  d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 0 0-16.536-1.84M7.5 14.25 5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1-1.5 0 .75.75 0 0 1 1.5 0Z" />
            </svg>
         </div>

         <p>سبد خرید شما خالی است</p>

         <a href="/shop/frontend/index.php" class="btn-outline">
            مشاهده محصولات
         </a>

      </div>

      <!-- محتوای سبد خرید -->
      <div class="cart-layout">

         <!-- محصولات -->
         <div id="cart-items" class="cart-items"></div>

         <!-- خلاصه سفارش -->
         <aside id="cart-summary" class="cart-summary" style="display: none;">

            <h2>خلاصه سفارش</h2>

            <div class="cart-summary-row">
               <span>جمع کل</span>

               <strong id="cart-total-price">
                  ۰ تومان
               </strong>
            </div>

            <a href="checkout.php" class="btn-primary">
               ادامه فرآیند خرید
            </a>

         </aside>

      </div>

   </main>

   <!-- فایل‌های جاوااسکریپت سبد خرید -->
   <script src="/shop/frontend/js/cart/cart.js"></script>
   <script src="/shop/frontend/js/cart/cart-page.js"></script>

</body>

</html>