<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

// frontend/php/comments/submit_comment.php
//
// این فایل نظر کاربر لاگین‌کرده رو تو دیتابیس ذخیره می‌کنه

session_start();

require_once __DIR__ . '/../../../backend/db.php';


// _______________________________ بررسی لاگین بودن کاربر _______________________________

if (!isset($_SESSION['user_id'])) {
   header('Location: ../auth/login.php');
   exit;
}


// _______________________________ خوندن و اعتبارسنجی پیام _______________________________

$message = trim($_POST['message'] ?? '');

// آدرس بازگشت، همون صفحه‌ای که کاربر ازش فرم رو فرستاده
$backUrl = $_SERVER['HTTP_REFERER'] ?? '../../index.php';

if ($message === '') {
   header('Location: ' . $backUrl);
   exit;
}


// _______________________________ ذخیره نظر در دیتابیس _______________________________

$stmt = $conn->prepare("INSERT INTO comments (user_id, name, message) VALUES (?, ?, ?)");

if ($stmt === false) {
   die('خطا تو prepare: ' . $conn->error);
}

$stmt->bind_param('iss', $_SESSION['user_id'], $_SESSION['user_name'], $message);
$stmt->execute();
$stmt->close();

header('Location: ' . $backUrl);
exit;