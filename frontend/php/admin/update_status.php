<?php
// frontend/php/admin/update_status.php
// تغییر وضعیت سفارش توسط ادمین

header('Content-Type: application/json; charset=utf-8');
session_start();

require_once __DIR__ . '/../../../backend/db.php';

// فقط متد POST مجاز است
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'متد غیرمجاز']);
    exit;
}

// فقط ادمین اجازه داره
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'دسترسی غیرمجاز']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$orderId = intval($input['order_id'] ?? 0);
$newStatus = $input['status'] ?? '';

// فقط این مقادیر مجازن (جلوی ثبت هر رشته‌ی دلخواه رو می‌گیره)
$allowedStatuses = ['processing', 'shipped', 'delivered', 'cancelled'];

if ($orderId <= 0 || !in_array($newStatus, $allowedStatuses)) {
    echo json_encode(['success' => false, 'message' => 'ورودی نامعتبر']);
    exit;
}

$stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
$stmt->bind_param('si', $newStatus, $orderId);
$stmt->execute();

if ($stmt->affected_rows === 0 && $conn->error) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'خطا در آپدیت وضعیت']);
    $stmt->close();
    exit;
}

$stmt->close();

echo json_encode(['success' => true]);