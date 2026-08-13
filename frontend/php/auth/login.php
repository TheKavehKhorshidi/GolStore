<?php

session_start();

require '../../../backend/db.php';

$error = "";


if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim($_POST["email"]);
    $password = $_POST["password"];


    // بررسی خالی نبودن فیلدها
    if (empty($email) || empty($password)) {

        $error = "لطفاً ایمیل و رمز عبور را وارد کنید.";

    } else {

        // پیدا کردن کاربر
        $stmt = $conn->prepare(
            "SELECT id, name, email, password, role
             FROM users
             WHERE email = ?"
        );

        $stmt->bind_param("s", $email);

        $stmt->execute();

        $result = $stmt->get_result();


        // بررسی وجود کاربر
        if ($result->num_rows === 0) {

            $error = "ایمیل یا رمز عبور اشتباه است.";

        } else {

            // دریافت اطلاعات کاربر
            $user = $result->fetch_assoc();


            // بررسی رمز عبور
            if (!password_verify($password, $user["password"])) {

                $error = "ایمیل یا رمز عبور اشتباه است.";

            } else {

                // ساخت Session
                $_SESSION["user_id"] = $user["id"];
                $_SESSION["user_name"] = $user["name"];
                $_SESSION["user_email"] = $user["email"];
                $_SESSION["user_role"] = $user["role"];


                // ورود موفق
                if ($user["role"] === "admin") {

                    header("Location: ../../index.php");
                    exit;

                } else {

                    header("Location: ../../index.php");
                    exit;

                }

            }
        }

        $stmt->close();
    }
}

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


         <!-- Header -->

         <div class="AuthHeader">

            <h2>خوش آمدید</h2>

            <p>
               برای ورود به حساب کاربری خود اطلاعاتتان را وارد کنید.
            </p>

         </div>


         <!-- Error -->

         <?php if (!empty($error)): ?>

            <div class="AuthError">
               <?php echo $error; ?>
            </div>

         <?php endif; ?>


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
                  autocomplete="current-password"
               >

            </div>


            <!-- Button -->

            <button
               type="submit"
               class="AuthButton"
            >
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