<?php
require '../../partials/config.php';
session_start();

// فقط ادمین اجازه ورود داره
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
   header('Location: ' . $baseUrl . 'index.php');
   exit;
}

require_once __DIR__ . '/../../../backend/db.php';


// _______________________________ خوندن محصولات از دیتابیس _______________________________

// خوندن همه محصولات، جدیدترین اول
$result = $conn->query("SELECT * FROM products ORDER BY id DESC");
$products = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="fa" dir="rtl">

<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">

   <title>مدیریت محصولات</title>

   <link rel="stylesheet" href="/shop/frontend/css/style.css">
   <link rel="stylesheet" href="/shop/frontend/css/admin/admin.css">
   <link rel="stylesheet" href="/shop/frontend/css/header/header.css">
</head>

<body>

   <?php include '../../partials/header/header.php'; ?>

   <main class="container admin-page">

      <div class="cart-header">
         <h1 class="section-title">پنل مدیریت</h1>
         <p class="cart-subtitle">مدیریت محصولات</p>
      </div>
      
      <!-- منوی بین صفحات پنل مدیریت -->
      <div class="admin-nav">
         <a href="index.php" class="admin-nav-item">سفارش‌ها</a>
         <a href="products.php" class="admin-nav-item active">محصولات</a>
         <a href="users.php" class="admin-nav-item">کاربران</a>
      </div>

      <div class="admin-toolbar">
         <a href="product_form.php" class="btn-primary">+ افزودن محصول جدید</a>
      </div>

      <!-- جدول محصولات -->
      <div class="admin-table-wrapper">
         <table class="admin-table">
            <thead>
               <tr>
                  <th>عکس</th>
                  <th>نام محصول</th>
                  <th>دسته‌بندی</th>
                  <th>قیمت</th>
                  <th>عملیات</th>
               </tr>
            </thead>
            <tbody>
               <?php if (count($products) === 0): ?>
                  <tr>
                     <td colspan="5" class="admin-empty-row">هنوز محصولی ثبت نشده</td>
                  </tr>
               <?php else: ?>
                  <?php foreach ($products as $product): ?>
                     <tr>
                        <td><img src="/shop/frontend/<?php echo htmlspecialchars($product['image']); ?>" class="admin-product-img"></td>
                        <td><?php echo htmlspecialchars($product['name']); ?></td>
                        <td><?php echo htmlspecialchars($product['category']); ?></td>
                        <td><?php echo number_format($product['price']); ?> تومان</td>
                        <td class="admin-actions-cell">
                           <!-- دکمه ویرایش، همون فرم محصول رو با id این محصول باز می‌کنه -->
                           <a href="product_form.php?id=<?php echo $product['id']; ?>" class="admin-edit-btn">ویرایش</a>

                           <!-- دکمه حذف، با جاوااسکریپت (products.js) درخواست حذف رو می‌فرسته -->
                           <button class="admin-delete-btn" data-id="<?php echo $product['id']; ?>">حذف</button>
                        </td>
                     </tr>
                  <?php endforeach; ?>
               <?php endif; ?>
            </tbody>
         </table>
      </div>

   </main>

   <script src="/shop/frontend/js/admin/products.js"></script>

</body>

</html>