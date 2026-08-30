<?php
// frontend/php/admin/delete_product.php
//
// این فایل یه محصول رو کامل از دیتابیس و عکسش رو از روی سرور حذف می‌کنه

header('Content-Type: application/json; charset=utf-8');
session_start();

require_once __DIR__ . '/../../../backend/db.php';


// _______________________________ بررسی متد و دسترسی ادمین _______________________________

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
   http_response_code(405);
   echo json_encode(['success' => false, 'message' => 'متد غیرمجاز']);
   exit;
}

// فقط ادمین لاگین‌شده اجازه حذف محصول رو داره
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
   http_response_code(403);
   echo json_encode(['success' => false, 'message' => 'دسترسی غیرمجاز']);
   exit;
}


// _______________________________ خوندن ایدی محصول _______________________________

$input = json_decode(file_get_contents('php://input'), true);
$id = (int) ($input['id'] ?? 0);

if ($id <= 0) {
   echo json_encode(['success' => false, 'message' => 'شناسه نامعتبر']);
   exit;
}


// _______________________________ پیدا کردن محصول (برای گرفتن آدرس عکسش) _______________________________

$stmt = $conn->prepare("SELECT image FROM products WHERE id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$product) {
   echo json_encode(['success' => false, 'message' => 'محصول یافت نشد']);
   exit;
}


// _______________________________ حذف محصول از دیتابیس _______________________________

$stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$stmt->close();


// _______________________________ حذف عکس محصول از روی سرور _______________________________

// اگه محصول عکس داشت، فایلش رو هم از روی سرور پاک می‌کنیم
if (!empty($product['image'])) {
   $imagePath = __DIR__ . '/../../' . $product['image'];

   if (file_exists($imagePath)) {
      unlink($imagePath);
   }
}

echo json_encode(['success' => true]);