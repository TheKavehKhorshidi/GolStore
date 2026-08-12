<?php
require '../../../backend/db.php';
require '../../partials/config.php';

// اگه توی آدرس چیزی مثل ?category=... بود، بگیرش. اگه نبود، مقدارش می‌شه null
$category = $_GET['category'] ?? null;

if ($category) {
   // فقط محصولات همون دسته رو بیار
   $stmt = $conn->prepare("SELECT * FROM products WHERE category = ?");
   $stmt->bind_param("s", $category);
   $stmt->execute();
   $result = $stmt->get_result();
} else {
   // هیچ دسته‌ای انتخاب نشده، همه محصولات رو بیار
   $result = $conn->query("SELECT * FROM products");
}
?>

<!DOCTYPE html>
<html lang="fa" dir="rtl">

<head>
   <meta charset="UTF-8">
   <title>محصولات - GolStore</title>
   <link rel="stylesheet" href="../../css/style.css">
   <link rel="stylesheet" href="../../css/homeStyles/header.css">
   <link rel="stylesheet" href="../../css/homeStyles/Best-selling.css">
   <link rel="stylesheet" href="../../css/homeStyles/footer.css">
</head>

<body>


   <!------------------------------------------------ Header ------------------------------------------------>
   <?php include '../../partials/header/header.php'; ?>
   <!------------------------------------------------------------------------------------------------------->


   <!------------------------------------------------ Main ------------------------------------------------>
   <main>
      <div class="container">
         <h3 class="section-title">
            <?php echo $category ? htmlspecialchars($category) : "همه محصولات"; ?>
         </h3>

         <div class="ProductsGrid">
            <?php while ($product = $result->fetch_assoc()): ?>
               <div class="ProductCard">
                  <img class="ProductImg" src="../../<?php echo htmlspecialchars($product['image']); ?>"
                     alt="<?php echo htmlspecialchars($product['name']); ?>">
                  <p class="ProductName"><?php echo htmlspecialchars($product['name']); ?></p>
                  <p class="ProductPrice"><?php echo number_format($product['price']); ?> تومان</p>
                  <button class="AddToCartBtn">افزودن به سبد خرید</button>
               </div>
            <?php endwhile; ?>
         </div>
      </div>
   </main>
   <!------------------------------------------------------------------------------------------------------->


   <!------------------------------------------------ Footer ------------------------------------------------>
   <?php include '../../partials/footer/footer.php'; ?>
   <!------------------------------------------------------------------------------------------------------->

</body>

</html>