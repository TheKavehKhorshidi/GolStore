<?php
require '../../../partials/config.php';
session_start();

require_once __DIR__ . '/../../../../backend/db.php';


// _______________________________ بررسی دسترسی ادمین _______________________________

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
   header('Location: ' . $baseUrl . 'index.php');
   exit;
}


// _______________________________ خوندن نظرات از دیتابیس _______________________________

$result = $conn->query("SELECT * FROM comments ORDER BY created_at DESC");
$comments = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="fa" dir="rtl">

<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">

   <title>پنل مدیریت - نظرات</title>

   <link rel="stylesheet" href="/shop/frontend/css/style.css">
   <link rel="stylesheet" href="/shop/frontend/css/admin/admin.css">
   <link rel="stylesheet" href="/shop/frontend/css/header/header.css">
</head>

<body>

   <?php include '../../../partials/header/header.php'; ?>

   <main class="container admin-page">

      <div class="cart-header">
         <h1 class="section-title">پنل مدیریت</h1>
         <p class="cart-subtitle">نظرات کاربران</p>
      </div>

      <div class="admin-nav">
         <a href="../index.php" class="admin-nav-item">سفارش‌ها</a>
         <a href="../products/products.php" class="admin-nav-item">محصولات</a>
         <a href="../users/users.php" class="admin-nav-item">کاربران</a>
         <a href="comments.php" class="admin-nav-item active">نظرات</a>
      </div>

      <div class="admin-table-wrapper">
         <table class="admin-table">
            <thead>
               <tr>
                  <th>کاربر</th>
                  <th>متن نظر</th>
                  <th>تاریخ</th>
               </tr>
            </thead>
            <tbody>
               <?php if (count($comments) === 0): ?>
                  <tr>
                     <td colspan="3" class="admin-empty-row">هنوز نظری ثبت نشده</td>
                  </tr>
               <?php else: ?>
                  <?php foreach ($comments as $comment): ?>
                     <tr>
                        <td><?php echo htmlspecialchars($comment['name']); ?></td>
                        <td><?php echo nl2br(htmlspecialchars($comment['message'])); ?></td>
                        <td><?php echo date('Y/m/d - H:i', strtotime($comment['created_at'])); ?></td>
                     </tr>
                  <?php endforeach; ?>
               <?php endif; ?>
            </tbody>
         </table>
      </div>

   </main>

</body>

</html>