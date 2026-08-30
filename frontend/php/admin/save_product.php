<?php
// frontend/php/admin/save_product.php
//
// این فایل هم محصول جدید ذخیره می‌کنه، هم محصول موجود رو آپدیت می‌کنه
// (بستگی به اینکه فرم با id ارسال شده باشه یا نه)

session_start();

require_once __DIR__ . '/../../../backend/db.php';


// _______________________________ بررسی دسترسی و متد _______________________________

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
   header('Location: ../../index.php');
   exit;
}

// فقط POST مجازه
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
   header('Location: products.php');
   exit;
}


// _______________________________ خوندن اطلاعات فرم _______________________________

$id = (int) ($_POST['id'] ?? 0);
$name = trim($_POST['name'] ?? '');
$category = trim($_POST['category'] ?? '');
$price = (int) ($_POST['price'] ?? 0);
$description = trim($_POST['description'] ?? '');

// اگه id بزرگتر از صفر باشه، یعنی داریم یه محصول موجود رو ویرایش می‌کنیم
$isEdit = $id > 0;

// آدرس بازگشت به فرم، برای زمانی که خطایی پیش بیاد
$backLink = 'product_form.php' . ($isEdit ? '?id=' . $id : '');

// اعتبارسنجی ساده روی فیلدهای اصلی
if ($name === '' || $category === '' || $price <= 0) {
   die('اطلاعات وارد شده نامعتبر است. <a href="' . $backLink . '">بازگشت</a>');
}


// _______________________________ آپلود عکس (اگه فایلی انتخاب شده باشه) _______________________________

// اگه عکس جدید آپلود نشه، این مقدار null می‌مونه (یعنی عکس قبلی محصول دست‌نخورده می‌مونه)
$imagePath = null;

if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {

   // فقط این سه نوع فایل عکس مجازن
   $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
   $fileType = mime_content_type($_FILES['image']['tmp_name']);

   if (!in_array($fileType, $allowedTypes)) {
      die('فقط فایل عکس (jpg, png, webp) مجاز است. <a href="' . $backLink . '">بازگشت</a>');
   }

   // یه اسم یکتا برای فایل می‌سازیم تا با عکس‌های قبلی تداخل نداشته باشه
   $extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
   $fileName = uniqid('product_') . '.' . $extension;

   $uploadDir = __DIR__ . '/../../image/products/';
   $uploadPath = $uploadDir . $fileName;

   if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath)) {
      $imagePath = 'image/products/' . $fileName;
   } else {
      die('خطا در آپلود عکس. <a href="' . $backLink . '">بازگشت</a>');
   }

} elseif (!$isEdit) {
   // تو حالت افزودن محصول جدید، انتخاب عکس اجباریه
   die('لطفاً یک عکس انتخاب کنید. <a href="product_form.php">بازگشت</a>');
}


// _______________________________ ذخیره نهایی در دیتابیس _______________________________

if ($isEdit) {

   if ($imagePath !== null) {
      // هم اطلاعات محصول عوض شده، هم عکس جدید آپلود شده
      $stmt = $conn->prepare("UPDATE products SET name=?, category=?, price=?, description=?, image=? WHERE id=?");
      $stmt->bind_param('ssissi', $name, $category, $price, $description, $imagePath, $id);
   } else {
      // عکس عوض نشده، فقط بقیه اطلاعات آپدیت میشن
      $stmt = $conn->prepare("UPDATE products SET name=?, category=?, price=?, description=? WHERE id=?");
      $stmt->bind_param('ssisi', $name, $category, $price, $description, $id);
   }

} else {
   // محصول کاملا جدیده، یه ردیف تازه اضافه می‌کنیم
   $stmt = $conn->prepare("INSERT INTO products (name, category, price, description, image) VALUES (?, ?, ?, ?, ?)");
   $stmt->bind_param('ssiss', $name, $category, $price, $description, $imagePath);
}

$stmt->execute();
$stmt->close();

header('Location: products.php');
exit;