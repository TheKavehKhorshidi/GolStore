<?php
session_start();
// تمام اطلاعاتی که ذخیره شده را پاک می‌کند.
session_unset();
// خود Session را کاملاً از بین می‌برد.
session_destroy();
?>



<script>
   // عدد تعداد محصولات خریداری شده رو ضفر میکند
   localStorage.removeItem("shop_cart");
   // یعد از انجام این کار ها کار را به صفحه مورد نظر انتقال میدهد
   window.location.href = "../../index.php";
</script>