<?php

session_start();

require '../../../backend/db.php';

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

   $name = trim($_POST["name"]);
   $email = trim($_POST["email"]);
   $password = $_POST["password"];
   $confirm_password = $_POST["confirm_password"];


   // بررسی خالی نبودن فیلدها
   if (
      empty($name) ||
      empty($email) ||
      empty($password) ||
      empty($confirm_password)
   ) {

      $error = "لطفاً همه فیلدها را پر کنید.";

   }


   // بررسی یکسان بودن رمزها
   elseif ($password !== $confirm_password) {

      $error = "رمز عبور و تکرار رمز عبور یکسان نیستند.";

   }


   else {

      // بررسی وجود ایمیل
      $checkEmail = $conn->prepare(
         "SELECT id FROM users WHERE email = ?"
      );

      $checkEmail->bind_param("s", $email);

      $checkEmail->execute();

      $result = $checkEmail->get_result();


      if ($result->num_rows > 0) {

         $error = "این ایمیل قبلاً ثبت نام کرده است.";

      }


      else {

         // رمزنگاری رمز عبور
         $hashedPassword = password_hash(
            $password,
            PASSWORD_DEFAULT
         );


         // ذخیره کاربر
         $stmt = $conn->prepare(
            "INSERT INTO users (name, email, password)
             VALUES (?, ?, ?)"
         );

         $stmt->bind_param(
            "sss",
            $name,
            $email,
            $hashedPassword
         );


         if ($stmt->execute()) {

            // ID کاربر جدید
            $userId = $stmt->insert_id;

            // ساخت Session کاربر
            $_SESSION["user_id"] = $userId;
            $_SESSION["user_name"] = $name;
            $_SESSION["user_email"] = $email;
            $_SESSION["user_role"] = "user";

            // انتقال به صفحه اصلی
            header("Location: ../../index.php");
            exit;

         } else {

            $error = "خطایی در ثبت نام رخ داد.";

         }


         $stmt->close();

      }


      $checkEmail->close();

   }

}
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

            <img
               class="logoImage"
               src="../../image/logo/rose.png"
               alt=""
            >

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


         <!-- Error -->

         <?php if (!empty($error)): ?>

            <div class="AuthError">
               <?php echo htmlspecialchars($error); ?>
            </div>

         <?php endif; ?>


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
                  placeholder="مثلاً یگانه الماسی"
                  autocomplete="name"
                  value="<?php echo htmlspecialchars($name ?? ''); ?>"
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
                  value="<?php echo htmlspecialchars($email ?? ''); ?>"
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


            <!-- Button -->

            <button
               type="submit"
               class="AuthButton"
            >
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