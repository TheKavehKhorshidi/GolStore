<?php
$host    = "localhost";
$db_user = "root";
$db_pass = "";
$db_name = "shop_db";

$conn = new mysqli($host, $db_user, $db_pass, $db_name);

if ($conn->connect_error) {
    die("خطا در اتصال به دیتابیس: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");
?>