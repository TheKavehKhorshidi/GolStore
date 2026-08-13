<?php
// frontend/php/cart/cart_get.php
// ورودی: JSON شامل آرایه‌ای از id محصولات → { "ids": [1, 2, 3] }
// خروجی: اطلاعات کامل و به‌روز هر محصول (برای نمایش در صفحه سبد خرید)

header('Content-Type: application/json; charset=utf-8');

// مسیر db.php رو با توجه به محل واقعی فایل در پروژه‌تون تنظیم کنید
require_once __DIR__ . '/../../../backend/db.php';

// فقط متد POST مجاز است
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'متد غیرمجاز']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$ids = $input['ids'] ?? [];

// اعتبارسنجی: باید آرایه غیرخالی باشه
if (!is_array($ids) || count($ids) === 0) {
    echo json_encode(['success' => true, 'products' => []]);
    exit;
}

// فقط عدد صحیح مثبت قبول می‌کنیم (جلوگیری از ورودی مخرب)
$ids = array_filter($ids, fn($id) => is_numeric($id) && $id > 0);
$ids = array_map('intval', $ids);

if (count($ids) === 0) {
    echo json_encode(['success' => true, 'products' => []]);
    exit;
}

// ساخت placeholder های امن برای IN (...) با mysqli prepared statement
$placeholders = implode(',', array_fill(0, count($ids), '?'));
$types = str_repeat('i', count($ids)); // همه ورودی‌ها عدد صحیح (integer) هستند

$sql = "SELECT id, name, price, image, category, description FROM products WHERE id IN ($placeholders)";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'خطا در آماده‌سازی کوئری']);
    exit;
}

// bind_param به‌صورت داینامیک (چون تعداد id ها متغیره)
$stmt->bind_param($types, ...$ids);
$stmt->execute();

$result = $stmt->get_result();
$products = [];
while ($row = $result->fetch_assoc()) {
    $products[] = $row;
}

$stmt->close();

echo json_encode(['success' => true, 'products' => $products]);