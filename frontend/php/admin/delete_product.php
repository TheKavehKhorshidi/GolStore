<?php
// frontend/php/admin/delete_product.php
// حذف محصول

header('Content-Type: application/json; charset=utf-8');
session_start();

require_once __DIR__ . '/../../../backend/db.php';

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

$input = json_decode(file_get_contents('php://input'), true);
$id = intval($input['id'] ?? 0);

if ($id <= 0) {
    echo json_encode(['success' => false, 'message' => 'شناسه نامعتبر']);
    exit;
}

$stmt = $conn->prepare("SELECT image FROM products WHERE id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$product) {
    echo json_encode(['success' => false, 'message' => 'محصول یافت نشد']);
    exit;
}

$stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$stmt->close();

if (!empty($product['image'])) {
    $imagePath = __DIR__ . '/../../' . $product['image'];
    if (file_exists($imagePath)) {
        unlink($imagePath);
    }
}

echo json_encode(['success' => true]);