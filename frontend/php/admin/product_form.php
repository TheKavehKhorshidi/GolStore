<?php
require '../../partials/config.php';
session_start();

// فقط ادمین اجازه ورود داره
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
   header('Location: ' . $baseUrl . 'index.php');
   exit;
}

require_once __DIR__ . '/../../../backend/db.php';


// _______________________________ تشخیص حالت افزودن یا ویرایش _______________________________

// این فرم دو حالت داره: افزودن محصول جدید، یا ویرایش یه محصول موجود
// اگه تو آدرس صفحه یه id داشته باشیم، یعنی حالت ویرایشه
$isEdit = false;

$product = [
   'id' => '',
   'name' => '',
   'category' => '',
   'price' => '',
   'description' => '',
   'image' => ''
];

if (isset($_GET['id'])) {
   $id = (int) $_GET['id'];

   $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
   $stmt->bind_param('i', $id);
   $stmt->execute();
   $result = $stmt->get_result()->fetch_assoc();
   $stmt->close();

   // اگه محصولی با این id پیدا شد، فرم رو با اطلاعات همون محصول پر می‌کنیم
   if ($result) {
      $isEdit = true;
      $product = $result;
   }
}

// دسته‌بندی‌های موجود برای انتخاب تو فرم
$categories = ['گیاهان آپارتمانی', 'باکس گل', 'دسته گل'];
?>

<!DOCTYPE html>
<html lang="fa" dir="rtl">

<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">

   <title><?php echo $isEdit ? 'ویرایش محصول' : 'افزودن محصول'; ?></title>

   <link rel="stylesheet" href="/shop/frontend/css/style.css">
   <link rel="stylesheet" href="/shop/frontend/css/admin/admin.css">
   <link rel="stylesheet" href="/shop/frontend/css/checkout/checkout.css">
   <link rel="stylesheet" href="/shop/frontend/css/header/header.css">
</head>

<body>

   <?php include '../../partials/header/header.php'; ?>

   <main class="container admin-page">

      <div class="cart-header">
         <h1 class="section-title"><?php echo $isEdit ? 'ویرایش محصول' : 'افزودن محصول جدید'; ?></h1>
      </div>

      <div class="checkout-form-box admin-form-box">

         <!-- این فرم چه افزودن باشه چه ویرایش، همیشه به save_product.php ارسال میشه -->
         <form action="save_product.php" method="POST" enctype="multipart/form-data">

            <!-- تو حالت ویرایش، id محصول همراه فرم فرستاده میشه تا سرور بدونه کدوم محصول رو آپدیت کنه -->
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($product['id']); ?>">

            <div class="checkout-field">
               <label>نام محصول</label>
               <input type="text" name="name" value="<?php echo htmlspecialchars($product['name']); ?>" required>
            </div>

            <div class="checkout-field">
               <label>دسته‌بندی</label>
               <select name="category" required>
                  <option value="" disabled <?php echo empty($product['category']) ? 'selected' : ''; ?>>انتخاب کنید</option>
                  <?php foreach ($categories as $cat): ?>
                     <option value="<?php echo htmlspecialchars($cat); ?>" <?php echo $product['category'] === $cat ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($cat); ?>
                     </option>
                  <?php endforeach; ?>
               </select>
            </div>

            <div class="checkout-field">
               <label>قیمت (تومان)</label>
               <input type="number" name="price" min="0" value="<?php echo htmlspecialchars($product['price']); ?>" required>
            </div>

            <div class="checkout-field">
               <label>توضیحات</label>
               <textarea name="description" rows="4"><?php echo htmlspecialchars($product['description']); ?></textarea>
            </div>

            <div class="checkout-field">
               <label>عکس محصول</label>

               <!-- تو حالت ویرایش، عکس فعلی محصول رو نشون می‌دیم -->
               <?php if ($isEdit && $product['image']): ?>
                  <img src="/shop/frontend/<?php echo htmlspecialchars($product['image']); ?>" class="admin-current-img">
                  <p class="admin-img-hint">اگه عکس جدید انتخاب نکنی، همین عکس فعلی می‌مونه</p>
               <?php endif; ?>

               <!-- تو حالت افزودن، انتخاب عکس اجباریه؛ تو حالت ویرایش اختیاریه -->
               <input type="file" name="image" accept="image/png, image/jpeg, image/webp" <?php echo $isEdit ? '' : 'required'; ?>>
            </div>

            <button type="submit" class="btn-primary"><?php echo $isEdit ? 'ذخیره تغییرات' : 'افزودن محصول'; ?></button>
            <a href="products.php" class="btn-outline">انصراف</a>

         </form>

      </div>

   </main>

</body>

</html>