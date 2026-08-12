<?php
require '../../../backend/db.php';
?>

<!DOCTYPE html>
<html lang="fa" dir="rtl">

<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">

   <title>ثبت نام - GolStore</title>

   <link rel="stylesheet" href="../../css/style.css">
   <link rel="stylesheet" href="../../css/authStyles/auth.css">
</head>

<body>

   <main class="AuthPage">

      <div class="AuthCard">

         <!-- Logo -->
         <div class="AuthLogo">
            <div class="AuthLogoIcon">🌿</div>

            <div>
               <h1>گل‌استور</h1>
               <span>GolStore</span>
            </div>
         </div>


         <!-- Title -->
         <div class="AuthHeader">

            <h2>ایجاد حساب کاربری</h2>

            <p>
               برای ادامه خرید و استفاده از امکانات گل‌استور ثبت نام کنید.
            </p>

         </div>


         <!-- Register Form -->
         <form class="AuthForm" method="POST">

            <!-- Name -->
            <div class="FormGroup">

               <label for="name">
                  نام و نام خانوادگی
               </label>

               <input
                  type="text"
                  id="name"
                  name="name"
                  placeholder="مثلاً کاوه خورشیدی"
                  autocomplete="name"
               >

            </div>


            <!-- Email -->
            <div class="FormGroup">

               <label for="email">
                  ایمیل
               </label>

               <input
                  type="email"
                  id="email"
                  name="email"
                  placeholder="example@gmail.com"
                  autocomplete="email"
               >

            </div>


            <!-- Password -->
            <div class="FormGroup">

               <label for="password">
                  رمز عبور
               </label>

               <input
                  type="password"
                  id="password"
                  name="password"
                  placeholder="رمز عبور خود را وارد کنید"
                  autocomplete="new-password"
               >

            </div>


            <!-- Confirm Password -->
            <div class="FormGroup">

               <label for="confirm_password">
                  تکرار رمز عبور
               </label>

               <input
                  type="password"
                  id="confirm_password"
                  name="confirm_password"
                  placeholder="رمز عبور را دوباره وارد کنید"
                  autocomplete="new-password"
               >

            </div>


            <!-- Terms -->
            <div class="AuthTerms">

               <input
                  type="checkbox"
                  id="terms"
                  name="terms"
               >

               <label for="terms">
                  قوانین و شرایط گل‌استور را می‌پذیرم.
               </label>

            </div>


            <!-- Button -->
            <button type="submit" class="AuthButton">
               ایجاد حساب
            </button>

         </form>


         <!-- Login -->
         <div class="AuthFooter">

            <span>
               قبلاً حساب کاربری ساخته‌اید؟
            </span>

            <a href="login.php">
               ورود به حساب
            </a>

         </div>

      </div>

   </main>

</body>

</html>