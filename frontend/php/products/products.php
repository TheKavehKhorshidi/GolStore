<?php
require '../../../backend/db.php';
require '../../partials/config.php';

// گرفتن دسته‌بندی و متن جستجو از آدرس صفحه
$category = $_GET['category'] ?? null;
$search = trim($_GET['search'] ?? '');

// _______________________________ ساخت کوئری بر اساس دسته‌بندی و جستجو _______________________________

if ($category && $search !== '') {
   // هم دسته‌بندی انتخاب شده، هم متن جستجو وارد شده
   $stmt = $conn->prepare("SELECT * FROM products WHERE category = ? AND name LIKE ?");
   $searchTerm = '%' . $search . '%';
   $stmt->bind_param("ss", $category, $searchTerm);
   $stmt->execute();
   $result = $stmt->get_result();

} elseif ($category) {
   // فقط دسته‌بندی انتخاب شده
   $stmt = $conn->prepare("SELECT * FROM products WHERE category = ?");
   $stmt->bind_param("s", $category);
   $stmt->execute();
   $result = $stmt->get_result();

} elseif ($search !== '') {
   // فقط متن جستجو وارد شده
   $stmt = $conn->prepare("SELECT * FROM products WHERE name LIKE ?");
   $searchTerm = '%' . $search . '%';
   $stmt->bind_param("s", $searchTerm);
   $stmt->execute();
   $result = $stmt->get_result();

} else {
   // هیچ فیلتری نیست، همه محصولات رو بیار
   $result = $conn->query("SELECT * FROM products");
}
?>

<!DOCTYPE html>
<html lang="fa" dir="rtl">

<head>
   <meta charset="UTF-8">
   <title>محصولات - GolStore</title>
   <link rel="stylesheet" href="../../css/style.css">
   <link rel="stylesheet" href="../../css/home/home.css">

   <link rel="stylesheet" href="../../css/header/header.css"> <!-- header -->
   <link rel="stylesheet" href="../../css/footer/footer.css"> <!-- footer -->
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

         <!-- فرم جستجو - دسته‌بندی فعلی رو هم حفظ می‌کنه تا فیلتر از دست نره -->
         <form method="GET" class="ProductSearchForm">
            <?php if ($category): ?>
               <input type="hidden" name="category" value="<?php echo htmlspecialchars($category); ?>">
            <?php endif; ?>

            <input
               type="text"
               name="search"
               placeholder="جستجوی نام محصول..."
               value="<?php echo htmlspecialchars($search); ?>"
               class="ProductSearchInput">

            <button type="submit" class="ProductSearchBtn">جستجو</button>
         </form>

         <div class="ProductsGrid">
            <?php if ($result->num_rows === 0): ?>
               <p class="ProductsEmpty">محصولی یافت نشد</p>
            <?php else: ?>
               <?php while ($product = $result->fetch_assoc()): ?> <!-- یک ردیف از دیتابیس رو به صورت آرایه بردار -->
                  <div class="ProductCard">
                     <img class="ProductImg" src="../../<?php echo htmlspecialchars($product['image']); ?>"
                        alt="<?php echo htmlspecialchars($product['name']); ?>">
                     <p class="ProductName"><?php echo htmlspecialchars($product['name']); ?></p>
                     <p class="ProductPrice"><?php echo number_format($product['price']); ?> تومان</p>
                     <button onclick="addToCart(<?php echo $product['id']; ?>)" class="AddToCartBtn">افزودن به سبد خرید</button>
                  </div>
               <?php endwhile; ?>
            <?php endif; ?>
         </div>
      </div>
   </main>
   <!------------------------------------------------------------------------------------------------------->


   <!------------------------------------------------ Footer ------------------------------------------------>
   <?php include '../../partials/footer/footer.php'; ?>
   <!------------------------------------------------------------------------------------------------------->

</body>

</html>