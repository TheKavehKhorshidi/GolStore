<?php
// frontend/php/cart/cart_get.php
//
// این فایل اطلاعات کامل محصولات داخل سبد خرید رو برمیگردونه
// ورودی: { "ids": [1, 2, 3] }
// خروجی: لیست محصولات با اسم، قیمت، عکس و ...

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../../../backend/db.php';


// _______________________________ بررسی متد درخواست _______________________________

// این api فقط با متد POST کار می‌کنه
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
   http_response_code(405);
   echo json_encode(['success' => false, 'message' => 'متد غیرمجاز']);
   exit;
}


// _______________________________ خوندن و پاکسازی ورودی _______________________________

// خوندن دیتای ارسالی و گرفتن آرایه ایدی‌ها
$input = json_decode(file_get_contents('php://input'), true);
$ids = $input['ids'] ?? [];

// اگه آرایه نبود یا خالی بود، یه لیست خالی برگردون
if (!is_array($ids) || count($ids) === 0) {
   echo json_encode(['success' => true, 'products' => []]);
   exit;
}

// فقط عددهای صحیح و مثبت رو نگه می‌داریم (برای امنیت بیشتر)
$cleanIds = [];
foreach ($ids as $id) {
   if (is_numeric($id) && $id > 0) {
      $cleanIds[] = (int) $id;
   }
}

// اگه بعد از پاکسازی چیزی نموند، لیست خالی برگردون
if (count($cleanIds) === 0) {
   echo json_encode(['success' => true, 'products' => []]);
   exit;
}


// _______________________________ گرفتن محصولات از دیتابیس _______________________________

// چون تعداد ایدی‌ها متغیره، به همون تعداد "?" برای کوئری میسازیم
$placeholders = implode(',', array_fill(0, count($cleanIds), '?'));

// چون همه ورودی‌ها عدد صحیح هستن، تایپشون برای همه "i" میشه
$types = str_repeat('i', count($cleanIds));

$sql = "SELECT id, name, price, image, category, description FROM products WHERE id IN ($placeholders)";
$stmt = $conn->prepare($sql);

if (!$stmt) {
   http_response_code(500);
   echo json_encode(['success' => false, 'message' => 'خطا در آماده‌سازی کوئری']);
   exit;
}

// دادن ایدی‌ها به کوئری (به‌صورت داینامیک، چون تعدادشون متغیره)
$stmt->bind_param($types, ...$cleanIds);
$stmt->execute();

$result = $stmt->get_result();

// جمع کردن نتیجه‌ها تو یه آرایه ساده
$products = [];
while ($row = $result->fetch_assoc()) {
   $products[] = $row;
}

$stmt->close();

echo json_encode(['success' => true, 'products' => $products]);