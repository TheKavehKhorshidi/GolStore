<?php
// frontend/php/checkout/checkout_process.php
// ثبت سفارش نهایی در دیتابیس بعد از پرداخت (شبیه‌سازی‌شده)

header('Content-Type: application/json; charset=utf-8');
session_start();

require_once __DIR__ . '/../../../backend/db.php';

// فقط متد POST مجاز است
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
   http_response_code(405);
   echo json_encode(['success' => false, 'message' => 'متد غیرمجاز']);
   exit;
}

// کاربر باید لاگین کرده باشه
if (!isset($_SESSION['user_id'])) {
   http_response_code(401);
   echo json_encode(['success' => false, 'message' => 'ابتدا وارد حساب کاربری خود شوید']);
   exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$cart = $input['cart'] ?? [];

if (!is_array($cart) || count($cart) === 0) {
   echo json_encode(['success' => false, 'message' => 'سبد خرید خالی است']);
   exit;
}

// استخراج id ها برای خوندن قیمت واقعی از دیتابیس
$ids = array_filter(array_column($cart, 'id'), fn($id) => is_numeric($id) && $id > 0);
$ids = array_map('intval', $ids);

if (count($ids) === 0) {
   echo json_encode(['success' => false, 'message' => 'سبد خرید نامعتبر است']);
   exit;
}

// خوندن محصولات واقعی از دیتابیس (نه از سمت کاربر)
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

// ترکیب تعداد (از سبد کاربر) با قیمت واقعی (از دیتابیس)
$orderItems = [];
$totalPrice = 0;

foreach ($cart as $cartItem) {
   $id = intval($cartItem['id'] ?? 0);
   $quantity = intval($cartItem['quantity'] ?? 0);

   if (!isset($dbProducts[$id]) || $quantity <= 0) {
      continue; // محصول نامعتبر یا حذف‌شده رو نادیده بگیر
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

// ساخت شماره پیگیری یکتا (مثلاً ORD-7F3K92A1)
function generateOrderNumber($conn)
{
   do {
      $orderNumber = 'ORD-' . strtoupper(substr(bin2hex(random_bytes(5)), 0, 8));
      $check = $conn->prepare("SELECT id FROM orders WHERE order_number = ?");
      $check->bind_param('s', $orderNumber);
      $check->execute();
      $exists = $check->get_result()->num_rows > 0;
      $check->close();
   } while ($exists); // اگر تصادفاً تکراری بود، دوباره بساز

   return $orderNumber;
}

$orderNumber = generateOrderNumber($conn);

// ثبت سفارش با تراکنش (transaction) تا اگر جایی خطا شد، هیچی نصفه‌کاره ذخیره نشه
$conn->begin_transaction();

try {
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

   // ثبت آیتم‌های سفارش
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

   $conn->commit();

   echo json_encode(['success' => true, 'order_number' => $orderNumber]);

} catch (Exception $e) {
   $conn->rollback();
   http_response_code(500);
   echo json_encode(['success' => false, 'message' => 'خطا در ثبت سفارش']);
}