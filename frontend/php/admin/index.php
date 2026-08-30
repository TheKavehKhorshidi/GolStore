<?php
require '../../partials/config.php';
session_start();

// فقط ادمین اجازه ورود داره
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: ' . $baseUrl . 'index.php');
    exit;
}

require_once __DIR__ . '/../../../backend/db.php';

// خوندن همه سفارش‌ها، جدیدترین اول
$result = $conn->query("SELECT * FROM orders ORDER BY created_at DESC");
$orders = $result->fetch_all(MYSQLI_ASSOC);

// متن فارسی هر وضعیت (برای نمایش)
$statusLabels = [
    'processing' => 'در حال پردازش',
    'shipped' => 'ارسال شده',
    'delivered' => 'تحویل داده شده',
    'cancelled' => 'لغو شده',
];
?>

<!DOCTYPE html>
<html lang="fa" dir="rtl">

<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">

   <title>پنل مدیریت</title>

   <link rel="stylesheet" href="/shop/frontend/css/style.css">
   <link rel="stylesheet" href="/shop/frontend/css/admin/admin.css">
   <link rel="stylesheet" href="/shop/frontend/css/header/header.css">
</head>

<body>

   <?php include '../../partials/header/header.php'; ?>

   <main class="container admin-page">

      <div class="cart-header">
         <h1 class="section-title">پنل مدیریت</h1>
         <p class="cart-subtitle">مدیریت سفارش‌ها</p>
      </div>

      <div class="admin-nav">
         <a href="index.php" class="admin-nav-item active">سفارش‌ها</a>
         <a href="products.php" class="admin-nav-item">محصولات</a>
      </div>

      <!-- جستجو -->
      <div class="admin-search-box">
         <input type="text" id="order-search" placeholder="جستجو بر اساس شماره سفارش، نام یا ایمیل خریدار...">
      </div>

      <!-- جدول سفارش‌ها -->
      <div class="admin-table-wrapper">
         <table class="admin-table">
            <thead>
               <tr>
                  <th>شماره پیگیری</th>
                  <th>خریدار</th>
                  <th>ایمیل</th>
                  <th>مبلغ کل</th>
                  <th>تاریخ</th>
                  <th>وضعیت</th>
               </tr>
            </thead>
            <tbody id="orders-table-body">
               <?php if (count($orders) === 0): ?>
                  <tr>
                     <td colspan="6" class="admin-empty-row">هنوز سفارشی ثبت نشده</td>
                  </tr>
               <?php else: ?>
                  <?php foreach ($orders as $order): ?>
                     <tr data-search-text="<?php echo htmlspecialchars(strtolower($order['order_number'] . ' ' . $order['customer_name'] . ' ' . $order['customer_email'])); ?>">
                        <td><?php echo htmlspecialchars($order['order_number']); ?></td>
                        <td><?php echo htmlspecialchars($order['customer_name']); ?></td>
                        <td><?php echo htmlspecialchars($order['customer_email']); ?></td>
                        <td><?php echo number_format($order['total_price']); ?> تومان</td>
                        <td><?php echo date('Y/m/d - H:i', strtotime($order['created_at'])); ?></td>
                        <td>
                           <select class="status-select" data-order-id="<?php echo $order['id']; ?>">
                              <?php foreach ($statusLabels as $value => $label): ?>
                                 <option value="<?php echo $value; ?>" <?php echo $order['status'] === $value ? 'selected' : ''; ?>>
                                    <?php echo $label; ?>
                                 </option>
                              <?php endforeach; ?>
                           </select>
                        </td>
                     </tr>
                  <?php endforeach; ?>
               <?php endif; ?>
            </tbody>
         </table>
      </div>

   </main>

   <script src="/shop/frontend/js/admin/admin.js"></script>

</body>

</html>