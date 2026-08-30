<?php
// frontend/php/admin/update_status.php
//
// این فایل وضعیت یه سفارش رو تغییر میده (مثلاً از "در حال پردازش" به "ارسال شده")

header('Content-Type: application/json; charset=utf-8');
session_start();

require_once __DIR__ . '/../../../backend/db.php';


// _______________________________ بررسی متد و دسترسی ادمین _______________________________

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
   http_response_code(405);
   echo json_encode(['success' => false, 'message' => 'متد غیرمجاز']);
   exit;
}

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
   http_response_code(403);
   echo json_encode(['success' => false, 'message' => 'دسترسی غیرمجاز']);
   exit;
}


// _______________________________ خوندن و اعتبارسنجی ورودی _______________________________

$input = json_decode(file_get_contents('php://input'), true);
$orderId = (int) ($input['order_id'] ?? 0);
$newStatus = $input['status'] ?? '';

// فقط این وضعیت‌ها مجازن، تا کسی نتونه یه رشته‌ی دلخواه رو تو دیتابیس ثبت کنه
$allowedStatuses = ['processing', 'shipped', 'delivered', 'cancelled'];

if ($orderId <= 0 || !in_array($newStatus, $allowedStatuses)) {
   echo json_encode(['success' => false, 'message' => 'ورودی نامعتبر']);
   exit;
}


// _______________________________ آپدیت وضعیت در دیتابیس _______________________________

$stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
$stmt->bind_param('si', $newStatus, $orderId);
$stmt->execute();

// اگه هیچ ردیفی آپدیت نشد و خطایی هم از سمت دیتابیس بود، یعنی مشکلی پیش اومده
if ($stmt->affected_rows === 0 && $conn->error) {
   http_response_code(500);
   echo json_encode(['success' => false, 'message' => 'خطا در آپدیت وضعیت']);
   $stmt->close();
   exit;
}

$stmt->close();

echo json_encode(['success' => true]);