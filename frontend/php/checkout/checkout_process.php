<?php
// frontend/php/checkout/checkout_process.php
//
// این فایل سفارش نهایی رو بعد از پرداخت (شبیه‌سازی‌شده) تو دیتابیس ثبت می‌کنه

header('Content-Type: application/json; charset=utf-8');
session_start();

require_once __DIR__ . '/../../../backend/db.php';


// _______________________________ بررسی متد و لاگین بودن کاربر _______________________________

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
   http_response_code(405);
   echo json_encode(['success' => false, 'message' => 'متد غیرمجاز']);
   exit;
}

if (!isset($_SESSION['user_id'])) {
   http_response_code(401);
   echo json_encode(['success' => false, 'message' => 'ابتدا وارد حساب کاربری خود شوید']);
   exit;
}


// _______________________________ خوندن سبد خرید ارسالی از کاربر _______________________________

$input = json_decode(file_get_contents('php://input'), true);
$cart = $input['cart'] ?? [];

if (!is_array($cart) || count($cart) === 0) {
   echo json_encode(['success' => false, 'message' => 'سبد خرید خالی است']);
   exit;
}

// فقط ایدی‌های عددی و معتبر رو جدا می‌کنیم
$ids = [];
foreach ($cart as $cartItem) {
   $id = $cartItem['id'] ?? null;

   if (is_numeric($id) && $id > 0) {
      $ids[] = (int) $id;
   }
}

if (count($ids) === 0) {
   echo json_encode(['success' => false, 'message' => 'سبد خرید نامعتبر است']);
   exit;
}


// _______________________________ خوندن قیمت واقعی محصولات از دیتابیس _______________________________

// نکته مهم: قیمت رو از دیتابیس می‌خونیم نه از دیتای ارسالی کاربر
// چون کاربر می‌تونه قیمت ارسالی رو از سمت مرورگر دستکاری کنه
$placeholders = implode(',', array_fill(0, count($ids), '?'));
$types = str_repeat('i', count($ids));

$stmt = $conn->prepare("SELECT id, name, price FROM products WHERE id IN ($placeholders)");
$stmt->bind_param($types, ...$ids);
$stmt->execute();
$result = $stmt->get_result();

$dbProducts = [];
while ($row = $result->fetch_assoc()) {
   $dbProducts[$row['id']] = $row;
}
$stmt->close();


// _______________________________ ساخت لیست نهایی سفارش _______________________________

$orderItems = [];
$totalPrice = 0;

foreach ($cart as $cartItem) {
   $id = (int) ($cartItem['id'] ?? 0);
   $quantity = (int) ($cartItem['quantity'] ?? 0);

   if (!isset($dbProducts[$id]) || $quantity <= 0) {
      continue;
   }

   $product = $dbProducts[$id];
   $lineTotal = $product['price'] * $quantity;
   $totalPrice += $lineTotal;

   $orderItems[] = [
      'product_id' => $id,
      'name' => $product['name'],
      'price' => $product['price'],
      'quantity' => $quantity
   ];
}

if (count($orderItems) === 0) {
   echo json_encode(['success' => false, 'message' => 'هیچ محصول معتبری در سبد یافت نشد']);
   exit;
}


// _______________________________ ساخت شماره پیگیری _______________________________

// یه شماره پیگیری تصادفی می‌سازیم، مثلاً ORD-7F3K92A1
// (دیگه چک نمی‌کنیم تکراری نباشه، چون احتمالش عملاً صفره)
$orderNumber = 'ORD-' . strtoupper(substr(bin2hex(random_bytes(5)), 0, 8));


// _______________________________ ثبت سفارش در دیتابیس _______________________________

// ثبت خود سفارش
$stmt = $conn->prepare(
   "INSERT INTO orders (order_number, user_id, customer_name, customer_email, total_price, status)
   VALUES (?, ?, ?, ?, ?, 'processing')"
);
$stmt->bind_param(
   'sissi',
   $orderNumber,
   $_SESSION['user_id'],
   $_SESSION['user_name'],
   $_SESSION['user_email'],
   $totalPrice
);
$stmt->execute();
$orderId = $stmt->insert_id;
$stmt->close();

// ثبت هر محصول داخل سفارش
$itemStmt = $conn->prepare(
   "INSERT INTO order_items (order_id, product_id, product_name, price, quantity)
   VALUES (?, ?, ?, ?, ?)"
);

foreach ($orderItems as $item) {
   $itemStmt->bind_param(
      'iisii',
      $orderId,
      $item['product_id'],
      $item['name'],
      $item['price'],
      $item['quantity']
   );
   $itemStmt->execute();
}
$itemStmt->close();

echo json_encode(['success' => true, 'order_number' => $orderNumber]);