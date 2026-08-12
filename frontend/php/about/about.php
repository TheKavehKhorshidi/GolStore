

<?php
require '../../partials/config.php';
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">

<head>
   <meta charset="UTF-8">
   <title>درباره ما - GolStore</title>
   <link rel="stylesheet" href="../../css/style.css">
   <link rel="stylesheet" href="../../css/homeStyles/header.css">
   <link rel="stylesheet" href="../../css/homeStyles/footer.css">
   <link rel="stylesheet" href="../../css/aboutStyles/about.css">
</head>

<body>

   <!------------------------------------------------ Header ------------------------------------------------>
   <?php include '../../partials/header/header.php'; ?>


   <!------------------------------------------------ Main ------------------------------------------------>
   <main class="main">

      <!-- Hero -->
      <div class="AboutHero">
         <div class="AboutHeroContent">
            <h1>داستان گل‌استور</h1>
            <p>از دل عشق به گیاهان شروع شد، حالا رسیده به خونه‌ی شما</p>
         </div>
      </div>

      <div class="container">

         <!-- Story -->
         <div class="AboutStory">
            <div class="AboutStoryImage">
               <img src="../../image/poster/4.png" alt="گل استور">
            </div>
            <div class="AboutStoryText">
               <h3 class="section-title">ما کی هستیم؟</h3>
               <p>
                  گل‌استور از یک علاقه‌ی ساده به گیاهان و گل‌ها شروع شد. ما باور داریم هر خونه‌ای با یک گلدون سبز،
                  یک دسته گل تازه یا حتی یک شاخه کوچیک، زنده‌تر و گرم‌تر می‌شه. تیم ما با دقت گیاهان و گل‌ها رو از
                  بهترین پرورش‌دهنده‌های ایرانی انتخاب می‌کنه تا مطمئن بشیم چیزی که به دستتون می‌رسه، سالم، تازه و
                  ماندگاره.
               </p>
               <p>
                  از روز اول هدفمون این بوده که خرید گل و گیاه رو ساده، مطمئن و لذت‌بخش کنیم — از انتخاب توی سایت
                  تا رسیدن به دم در خونه‌تون.
               </p>
            </div>
         </div>

         <!-- Values -->
         <h3 class="section-title">چرا گل‌استور؟</h3>
         <div class="AboutValuesGrid">

            <div class="AboutValueCard">
               <img class="AboutValueIcon" src="../../image/aboutIcons/4.svg" alt="کیفیت">
               <h4>کیفیت تضمینی</h4>
               <p>همه گیاهان با دقت انتخاب می‌شن و با ۳ ماه ضمانت سلامت به دستتون می‌رسن.</p>
            </div>

            <div class="AboutValueCard">
               <img class="AboutValueIcon" src="../../image/aboutIcons/2.svg" alt="ارسال">
               <h4>ارسال سریع و مطمئن</h4>
               <p>سفارشتون رو تا دم در خونه می‌رسونیم، تازه و سالم.</p>
            </div>

            <div class="AboutValueCard">
               <img class="AboutValueIcon" src="../../image/aboutIcons/1.svg" alt="اعتماد">
               <h4>پشتیبانی همیشگی</h4>
               <p>هر سوالی درباره نگهداری گیاهتون داشتید، تیم ما کنارتونه.</p>
            </div>

         </div>

         <!-- CTA -->
         <div class="AboutCTA">
            <h3>آماده‌اید یه گوشه از خونه‌تون رو سبز کنید؟</h3>
            <a href="<?php echo $baseUrl; ?>php/products/products.php" class="AboutCTABtn">مشاهده محصولات</a>
         </div>

      </div>

   </main>


   <!------------------------------------------------ Footer ------------------------------------------------>
   <?php include '../../partials/footer/footer.php'; ?>

</body>

</html>