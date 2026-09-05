<?php
require '../../../partials/config.php';
session_start();

require_once __DIR__ . '/../../../../backend/db.php';


// _______________________________ بررسی دسترسی ادمین _______________________________

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
   header('Location: ' . $baseUrl . 'index.php');
   exit;
}


// _______________________________ خوندن کاربرها از دیتابیس _______________________________

$result = $conn->query("SELECT id, name, email, created_at, role FROM users ORDER BY created_at DESC");
$users = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="fa" dir="rtl">

<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">

   <title>پنل مدیریت - کاربران</title>

   <link rel="stylesheet" href="/shop/frontend/css/style.css">
   <link rel="stylesheet" href="/shop/frontend/css/admin/admin.css">
   <link rel="stylesheet" href="/shop/frontend/css/header/header.css">
</head>

<body>

   <?php include '../../../partials/header/header.php'; ?>


   <main class="container admin-page">

      <div class="cart-header">
         <h1 class="section-title">پنل مدیریت</h1>
         <p class="cart-subtitle">مدیریت کاربران</p>
      </div>

      <div class="admin-nav">
         <a href="../index.php" class="admin-nav-item">سفارش‌ها</a>
         <a href="../products/products.php" class="admin-nav-item">محصولات</a>
         <a href="../users/users.php" class="admin-nav-item active">کاربران</a>
         <a href="../comments/comments.php" class="admin-nav-item">نظرات</a>

      </div>

      <div class="admin-table-wrapper">
         <table class="admin-table">
            <thead>
               <tr>
                  <th>نام</th>
                  <th>ایمیل</th>
                  <th>تاریخ ثبت‌نام</th>
                  <th>نقش</th>
               </tr>
            </thead>
            <tbody>
               <?php if (count($users) === 0): ?>
                  <tr>
                     <td colspan="4" class="admin-empty-row">هنوز کاربری ثبت‌نام نکرده</td>
                  </tr>
               <?php else: ?>
                  <?php foreach ($users as $user): ?>
                     <tr>
                        <td><?php echo htmlspecialchars($user['name']); ?></td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td><?php echo date('Y/m/d - H:i', strtotime($user['created_at'])); ?></td>
                        <td><?php echo $user['role'] === 'admin' ? 'ادمین' : 'مشتری'; ?></td>
                     </tr>
                  <?php endforeach; ?>
               <?php endif; ?>
            </tbody>
         </table>
      </div>

   </main>

</body>

</html>