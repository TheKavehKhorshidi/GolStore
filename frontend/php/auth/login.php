<?php
require '../../../backend/db.php';
?>

<!DOCTYPE html>
<html lang="fa" dir="rtl">

<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">

   <title>ورود - GolStore</title>

   <link rel="stylesheet" href="../../css/style.css">
   <link rel="stylesheet" href="../../css/authStyles/auth.css">
</head>

<body>

   <main class="AuthPage">

      <div class="AuthCard">

         <!-- Logo -->
         <div class="AuthLogo">
            <div class="AuthLogoIcon">🌿</div>
            <img src="../../image/logo/rose.png" alt="">

            <div>
               <h1>گل‌استور</h1>
               <span>GolStore</span>
            </div>
         </div>


         <!-- Header -->
         <div class="AuthHeader">

            <h2>خوش آمدید</h2>

            <p>
               برای ورود به حساب کاربری خود اطلاعاتتان را وارد کنید.
            </p>

         </div>


         <!-- Login Form -->
         <form class="AuthForm" method="POST">

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
                  autocomplete="current-password"
               >

            </div>


            <!-- Button -->
            <button type="submit" class="AuthButton">
               ورود به حساب
            </button>

         </form>


         <!-- Register -->
         <div class="AuthFooter">

            <span>
               هنوز حساب کاربری ندارید؟
            </span>

            <a href="register.php">
               ثبت نام
            </a>

         </div>

      </div>

   </main>

</body>

</html>