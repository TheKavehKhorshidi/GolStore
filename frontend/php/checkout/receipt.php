<?php
require '../../partials/config.php';
session_start();

require_once __DIR__ . '/../../../backend/db.php';


// _______________________________ بررسی لاگین و شماره سفارش _______________________________

// اگر کاربر لاگین نکرده بود، بفرستش صفحه ورود
if (!isset($_SESSION['user_id'])) {
   header('Location: ' . $baseUrl . 'php/auth/login.php');
   exit;
}

// شماره سفارش رو از آدرس صفحه می‌خونیم (مثلاً receipt.php?order=ORD-XXXX)
$orderNumber = $_GET['order'] ?? '';

if (empty($orderNumber)) {
   header('Location: ' . $baseUrl . 'index.php');
   exit;
}


// _______________________________ خوندن سفارش از دیتابیس _______________________________

$stmt = $conn->prepare("SELECT * FROM orders WHERE order_number = ?");
$stmt->bind_param('s', $orderNumber);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();
$stmt->close();

// اگر همچین سفارشی اصلا وجود نداشت
if (!$order) {
   header('Location: ' . $baseUrl . 'index.php');
   exit;
}

// نکته امنیتی مهم: چک می‌کنیم این سفارش برای همین کاربره
// وگرنه کسی می‌تونست با تغییر شماره سفارش تو آدرس، رسید بقیه رو ببینه
if ($order['user_id'] != $_SESSION['user_id']) {
   header('Location: ' . $baseUrl . 'index.php');
   exit;
}


// _______________________________ خوندن محصولات این سفارش _______________________________

$stmt = $conn->prepare("SELECT * FROM order_items WHERE order_id = ?");
$stmt->bind_param('i', $order['id']);
$stmt->execute();
$items = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<!DOCTYPE html>
<html lang="fa" dir="rtl">

<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">

   <title>رسید خرید</title>

   <link rel="stylesheet" href="/shop/frontend/css/style.css">
   <link rel="stylesheet" href="/shop/frontend/css/checkout/receipt.css">
   <link rel="stylesheet" href="/shop/frontend/css/header/header.css">
</head>

<body>

   <?php include '../../partials/header/header.php'; ?>

   <main class="container receipt-page">

      <div class="receipt-box">

         <!-- آیکون تیک موفقیت -->
         <div class="receipt-success-icon">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
               <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
            </svg>
         </div>

         <h1>پرداخت با موفقیت انجام شد</h1>
         <p class="receipt-order-number">
            شماره پیگیری: <strong><?php echo htmlspecialchars($order['order_number']); ?></strong>
         </p>

         <!-- اطلاعات کلی سفارش -->
         <div class="receipt-info-grid">
            <div class="receipt-info-item">
               <span>نام خریدار</span>
               <strong><?php echo htmlspecialchars($order['customer_name']); ?></strong>
            </div>
            <div class="receipt-info-item">
               <span>ایمیل</span>
               <strong><?php echo htmlspecialchars($order['customer_email']); ?></strong>
            </div>
            <div class="receipt-info-item">
               <span>تاریخ خرید</span>
               <strong><?php echo date('Y/m/d - H:i', strtotime($order['created_at'])); ?></strong>
            </div>
            <div class="receipt-info-item">
               <span>وضعیت</span>
               <strong class="receipt-status-success">پرداخت موفق</strong>
            </div>
         </div>

         <h2>محصولات خریداری‌شده</h2>

         <!-- لیست محصولات این سفارش -->
         <div class="receipt-items">
            <?php foreach ($items as $item): ?>
               <div class="receipt-item">
                  <div class="receipt-item-name"><?php echo htmlspecialchars($item['product_name']); ?></div>
                  <div class="receipt-item-qty">تعداد: <?php echo $item['quantity']; ?></div>
                  <div class="receipt-item-price">
                     <?php echo number_format($item['price'] * $item['quantity']); ?> تومان
                  </div>
               </div>
            <?php endforeach; ?>
         </div>

         <div class="receipt-total-row">
            <span>جمع کل پرداختی</span>
            <strong><?php echo number_format($order['total_price']); ?> تومان</strong>
         </div>

         <div class="receipt-actions">
            <button onclick="window.print()" class="btn-outline">چاپ رسید</button>
            <a href="/shop/frontend/index.php" class="btn-primary">بازگشت به فروشگاه</a>
         </div>

      </div>

   </main>

</body>

</html>