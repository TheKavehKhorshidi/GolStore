<?php
// frontend/php/admin/save_product.php
// ذخیره محصول جدید یا آپدیت محصول موجود

session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: ../../index.php');
    exit;
}

require_once __DIR__ . '/../../../backend/db.php';

// فقط POST مجازه
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: products.php');
    exit;
}

$id = intval($_POST['id'] ?? 0);
$name = trim($_POST['name'] ?? '');
$category = trim($_POST['category'] ?? '');
$price = intval($_POST['price'] ?? 0);
$description = trim($_POST['description'] ?? '');

$isEdit = $id > 0;

// اعتبارسنجی ساده
if ($name === '' || $category === '' || $price <= 0) {
    die('اطلاعات وارد شده نامعتبر است. <a href="product_form.php' . ($isEdit ? '?id=' . $id : '') . '">بازگشت</a>');
}

// ______________________ آپلود عکس (فقط اگه فایلی انتخاب شده باشه) ______________________
$imagePath = null; // اگه null بمونه یعنی عکس جدیدی آپلود نشده

if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {

    $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
    $fileType = mime_content_type($_FILES['image']['tmp_name']);

    if (!in_array($fileType, $allowedTypes)) {
        die('فقط فایل عکس (jpg, png, webp) مجاز است. <a href="product_form.php' . ($isEdit ? '?id=' . $id : '') . '">بازگشت</a>');
    }

    // ساخت اسم یکتا برای فایل تا با فایل‌های قبلی تداخل نداشته باشه
    $extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
    $fileName = uniqid('product_') . '.' . $extension;

    $uploadDir = __DIR__ . '/../../image/products/';
    $uploadPath = $uploadDir . $fileName;

    if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath)) {
        $imagePath = 'image/products/' . $fileName;
    } else {
        die('خطا در آپلود عکس. <a href="product_form.php' . ($isEdit ? '?id=' . $id : '') . '">بازگشت</a>');
    }

} elseif (!$isEdit) {
    // حالت افزودن محصول جدید، عکس اجباریه
    die('لطفاً یک عکس انتخاب کنید. <a href="product_form.php">بازگشت</a>');
}
// ___________________________________________________________________________________________________________________

if ($isEdit) {
    // ______________________ ویرایش محصول موجود ______________________
    if ($imagePath !== null) {
        // عکس جدید هم آپلود شده
        $stmt = $conn->prepare("UPDATE products SET name=?, category=?, price=?, description=?, image=? WHERE id=?");
        $stmt->bind_param('ssissi', $name, $category, $price, $description, $imagePath, $id);
    } else {
        // عکس عوض نشده، همون قبلی می‌مونه
        $stmt = $conn->prepare("UPDATE products SET name=?, category=?, price=?, description=? WHERE id=?");
        $stmt->bind_param('ssisi', $name, $category, $price, $description, $id);
    }
    $stmt->execute();
    $stmt->close();

} else {
    // ______________________ افزودن محصول جدید ______________________
    $stmt = $conn->prepare("INSERT INTO products (name, category, price, description, image) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param('ssiss', $name, $category, $price, $description, $imagePath);
    $stmt->execute();
    $stmt->close();
}

header('Location: products.php');
exit;