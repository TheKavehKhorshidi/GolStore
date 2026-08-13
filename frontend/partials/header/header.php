<?php session_start(); ?>

<header class="site-header">

   <div class="headerLogoAndLinkContainer">

      <div class="logo">
         <img src="<?php echo $baseUrl; ?>image/logo/rose.png" alt="GolStore Logo" class="logoImage">
         <div class="logoText">
            <p class="logoText1">GolStore</p>
         </div>
      </div>

      <div class="headerLinksContainer">
         <div class="headerLink"><a href="<?php echo $baseUrl; ?>index.php">خانه</a></div>
         <div class="headerLink"><a href="<?php echo $baseUrl; ?>php/products/products.php">محصولات</a></div>
         <div class="headerLink"><a
               href="<?php echo $baseUrl; ?>php/products/products.php?category=<?php echo urlencode('گیاهان آپارتمانی'); ?>">گیاهان
               آپارتمانی</a></div>
         <div class="headerLink"><a
               href="<?php echo $baseUrl; ?>php/products/products.php?category=<?php echo urlencode('دسته گل'); ?>">دسته
               گل</a></div>
         <div class="headerLink"><a
               href="<?php echo $baseUrl; ?>php/products/products.php?category=<?php echo urlencode('باکس گل'); ?>">باکس
               گل</a></div>
         <div class="headerLink"><a href="<?php echo $baseUrl; ?>php/about/about.php">تماس با ما</a></div>
      </div>

   </div>


   <div class="headerIconsContainer">

      <!-- Cart -->

      <div class="cartIconWrapper">
         <a href="<?php echo $baseUrl; ?>php/cart/cart.php">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
               stroke="currentColor" class="headerIcons">
               <path stroke-linecap="round" stroke-linejoin="round"
                  d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007ZM8.625 10.5a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm7.5 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
            </svg>
            <div class="cart-count-container">
               <span id="cart-count" class="cartDot"></span>
            </div>
         </a>
      </div>


      <!-- User -->

      <div class="UserMenu">

         <div class="UserButton">

            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
               stroke="currentColor" class="headerIcons">
               <path stroke-linecap="round" stroke-linejoin="round"
                  d="M17.982 18.725A7.488 7.488 0 0 0 12 15.75a7.488 7.488 0 0 0-5.982 2.975m11.963 0a9 9 0 1 0-11.963 0m11.963 0A8.966 8.966 0 0 1 12 21a8.966 8.966 0 0 1-5.982-2.275M15 9.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
            </svg>

         </div>


         <div class="UserDropdown">

            <?php if (isset($_SESSION["user_id"])): ?>

               <div class="UserInfo">

                  <strong>
                     <?php echo htmlspecialchars($_SESSION["user_name"]); ?>
                  </strong>

                  <span>
                     <?php echo htmlspecialchars($_SESSION["user_email"]); ?>
                  </span>

               </div>


               <?php if ($_SESSION["user_role"] === "admin"): ?>

                  <a href="<?php echo $baseUrl; ?>php/admin/index.php" class="UserMenuItem AdminItem">
                     پنل مدیریت
                  </a>

               <?php endif; ?>


               <a href="<?php echo $baseUrl; ?>php/auth/logout.php" class="UserMenuItem LogoutItem">
                  خروج از حساب
               </a>


            <?php else: ?>

               <div class="GuestInfo">

                  <span>وارد حساب کاربری نشده‌اید</span>

                  <a href="<?php echo $baseUrl; ?>php/auth/login.php">
                     ورود به حساب
                  </a>

               </div>

            <?php endif; ?>

         </div>

      </div>


   </div>

   <script src="<?php echo $baseUrl; ?>js/cart/cart.js"></script>
</header>