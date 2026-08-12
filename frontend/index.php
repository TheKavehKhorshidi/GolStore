<?php
require '../backend/db.php';
require 'partials/config.php';

$result = $conn->query("SELECT * FROM products");
?>

<!DOCTYPE html>
<html lang="fa" dir="rtl">

<head>
   <meta charset="UTF-8">
   <title>GolStore</title>
   <link rel="stylesheet" href="css/style.css">
   <link rel="stylesheet" href="css/homeStyles/header.css">
   <link rel="stylesheet" href="css/homeStyles/slider.css">
   <link rel="stylesheet" href="css/homeStyles/categories.css">
   <link rel="stylesheet" href="css/homeStyles/whySection.css">
   <link rel="stylesheet" href="css/homeStyles/Best-selling.css">
   <link rel="stylesheet" href="css/homeStyles/footer.css">
</head>

<body>

   <!------------------------------------------------ header ------------------------------------------------>
   <?php include 'partials/header/header.php'; ?>
   <!------------------------------------------------------------------------------------------------------->


   <!------------------------------------------------ main ------------------------------------------------>
   <main>

      <div class="container">
         <!-------------------------------------------------- slider -------------------------------------------------->
         <div class="slider-wrapper">
            <button id="nextBtn" class="slider-btn">‹</button>
            <div class="slider" id="mySlider">
               <div class="slider-item">
                  <img class="slider-img" src="image/poster/1.png" alt="">
                  <div class="slider-item-content">
                     <p>گل های آپارتمانی</p>
                     <button class="slider-img-btn">مشاهده</button>
                  </div>
               </div>
               <div class="slider-item">
                  <img class="slider-img" src="image/poster/2.jpg" alt="">
                  <div class="slider-item-content">
                     <p>باکس گل</p>
                     <button class="slider-img-btn">مشاهده</button>
                  </div>
               </div>
               <div class="slider-item">
                  <img class="slider-img" src="image/poster/3.png" alt="">
                  <div class="slider-item-content">
                     <p>همه محصولات</p>
                     <button class="slider-img-btn">مشاهده</button>
                  </div>
               </div>
            </div>
            <button id="prevBtn" class="slider-btn">›</button>
         </div>

         <!------------------------------------------------ Categories ------------------------------------------------>
         <h3 class="section-title">دسته بندی ها</h3>
         <div class="CategoriesContainer">
            <div class="category">
               <img class="CategoriesImg" src="image/Categories/McQueens12.04.2022_BT10740_PurpleVase.jpg" alt="">
               <p class="CategoriesTitle">همه محصولات</p>
            </div>
            <!--  -->
            <a href="php/products/products.php?category=<?php echo urlencode('گیاهان آپارتمانی'); ?>" class="category">
               <img class="CategoriesImg" src="image/Categories/Picture3_82df690a-d71d-4429-9c2b-62b3cfba5137.jpg"
                  alt="">
               <p class="CategoriesTitle">گل های آپارتمانی</p>
            </a>
            <!--  -->
            <div class="category">
               <img class="CategoriesImg" src="image/Categories/APC_0463-768x768.jpg" alt="">

               <p class="CategoriesTitle">باکس گل</p>
            </div>
            <div class="category">
               <img class="CategoriesImg" src="image/Categories/McQueens_12_01_2022_BT6341.jpg" alt="">
               <p class="CategoriesTitle">دسته گل</p>
            </div>
         </div>

         <!------------------------------------------------ Best-selling ------------------------------------------------>
         <div class="ProductsContainer">
            <h3 class="section-title">محصولات پر فروش</h3>

            <div class="ProductsGrid">

               <?php while ($product = $result->fetch_assoc()): ?>
                  <div class="ProductCard">
                     <img class="ProductImg" src="<?php echo htmlspecialchars($product['image']); ?>"
                        alt="<?php echo htmlspecialchars($product['name']); ?>">
                     <p class="ProductName"><?php echo htmlspecialchars($product['name']); ?></p>
                     <p class="ProductPrice"><?php echo number_format($product['price']); ?> تومان</p>
                     <button class="AddToCartBtn">افزودن به سبد خرید</button>
                  </div>
               <?php endwhile; ?>

            </div>
         </div>
      </div>

      <!------------------------------------------------ Why section ------------------------------------------------>
      <div class="WhyContainer">
         <div class="WhyTitle">
            <h3>چرا گیاهان خود را آنلاین از گل استور سفارش دهید؟</h3>
            <p>خوشحالیم که پرسیدید. این هم چند دلیل عالی!</p>
         </div>

         <div class="WhyDescriptionContainer">

            <div class="WhyRow">
               <div class="WhyItem">
                  <img class="WhyIcon" src="image/icons/local_shipping_24dp_F0F0F0_FILL0_wght400_GRAD0_opsz24.svg"
                     alt="ارسال">
                  <p class="WhyText">گیاهان خود را آنلاین سفارش دهید <span class="highlight">ما بقیه‌ی کار رو انجام
                        می‌دیم.</span></p>
               </div>

               <div class="WhyItem">
                  <img class="WhyIcon" src="image/icons/psychiatry_24dp_F0F0F0_FILL0_wght400_GRAD0_opsz24.svg"
                     alt="گیاه">
                  <p class="WhyText"><span class="highlight">دوستان سبز ما با دقت انتخاب می‌شوند</span> و تازه از بهترین
                     پرورش‌دهنده‌های ایرانی می‌رسند.</p>
               </div>
            </div>

            <div class="WhyRow">
               <div class="WhyItem">
                  <img class="WhyIcon" src="image/icons/devices_24dp_F0F0F0_FILL0_wght400_GRAD0_opsz24.svg"
                     alt="مرور آنلاین">
                  <p class="WhyText"><span class="highlight">راحت باشید و آرامش داشته باشید!</span> صدها گیاه و گلدان را
                     از خانه خود مرور کنید.</p>
               </div>

               <div class="WhyItem">
                  <img class="WhyIcon" src="image/icons/verified_24dp_F0F0F0_FILL0_wght400_GRAD0_opsz24.svg"
                     alt="گارانتی">
                  <p class="WhyText">گارانتی شامل حال شماست! ما <span class="highlight">۳ ماه ضمانت سلامت گیاه</span>
                     روی همه محصولات ارائه می‌دهیم.</p>
               </div>
            </div>

         </div>
      </div>

   </main>
   <!------------------------------------------------------------------------------------------------------->


   <!------------------------------------------------ footer ------------------------------------------------>
   <?php include 'partials/footer/footer.php'; ?>
   <!------------------------------------------------------------------------------------------------------->

   <script src="js/slider/slider.js"></script>
</body>

</html>