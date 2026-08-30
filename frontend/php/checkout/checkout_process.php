<?php
// frontend/php/checkout/checkout_process.php
//
// این فایل سفارش نهایی رو بعد از پرداخت (شبیه‌سازی‌شده) تو دیتابیس ثبت می‌کنه

header('Content-Type: application/json; charset=utf-8');
session_start();

require_once __DIR__ . '/../../../backend/db.php';


// _______________________________ بررسی متد و لاگین بودن کاربر _______________________________

// فقط متد POST مجاز است
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
   http_response_code(405);
   echo json_encode(['success' => false, 'message' => 'متد غیرمجاز']);
   exit;
}

// کاربر باید لاگین کرده باشه تا بتونه سفارش ثبت کنه
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

// از بین آیتم‌های سبد، فقط ایدی‌های عددی و معتبر رو جدا می‌کنیم
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

// محصولات رو بر اساس id تو یه آرایه ذخیره می‌کنیم تا راحت پیداشون کنیم
$dbProducts = [];
while ($row = $result->fetch_assoc()) {
   $dbProducts[$row['id']] = $row;
}
$stmt->close();


// _______________________________ ساخت لیست نهایی سفارش _______________________________

// تعداد هر محصول رو از سبد کاربر می‌گیریم، ولی قیمتش رو از دیتابیس
$orderItems = [];
$totalPrice = 0;

foreach ($cart as $cartItem) {
   $id = (int) ($cartItem['id'] ?? 0);
   $quantity = (int) ($cartItem['quantity'] ?? 0);

   // اگه محصول تو دیتابیس پیدا نشد یا تعدادش نامعتبر بود، ردش کن
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


// _______________________________ ساخت شماره پیگیری یکتا _______________________________

// یه شماره پیگیری تصادفی می‌سازه، مثلاً ORD-7F3K92A1
// و اگه تصادفاً تکراری بود، دوباره میسازه تا مطمئن بشه یکتاست
function generateOrderNumber($conn)
{
   do {
      $orderNumber = 'ORD-' . strtoupper(substr(bin2hex(random_bytes(5)), 0, 8));

      $check = $conn->prepare("SELECT id FROM orders WHERE order_number = ?");
      $check->bind_param('s', $orderNumber);
      $check->execute();
      $exists = $check->get_result()->num_rows > 0;
      $check->close();

   } while ($exists);

   return $orderNumber;
}

$orderNumber = generateOrderNumber($conn);


// _______________________________ ثبت نهایی سفارش در دیتابیس _______________________________

// از transaction استفاده می‌کنیم تا اگه یه جای کار خطا داد،
// نصفه‌کاره چیزی تو دیتابیس ذخیره نشه (یا همه ثبت میشه، یا هیچی)
$conn->begin_transaction();

try {
   // اول خود سفارش رو ثبت می‌کنیم
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

   // بعد، هر محصول داخل سفارش رو جدا ثبت می‌کنیم
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

   // همه چیز درست پیش رفت، پس تغییرات رو نهایی می‌کنیم
   $conn->commit();

   echo json_encode(['success' => true, 'order_number' => $orderNumber]);

} catch (Exception $e) {
   // اگه یه جایی خطا داد، همه چیز رو برمی‌گردونیم به حالت قبل
   $conn->rollback();
   http_response_code(500);
   echo json_encode(['success' => false, 'message' => 'خطا در ثبت سفارش']);
}