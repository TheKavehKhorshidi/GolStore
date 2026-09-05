// _______________________________ حذف محصول _______________________________

document.querySelectorAll('.admin-delete-btn').forEach(button => {
   button.addEventListener('click', async () => {
      const productId = button.dataset.id;

      // اول از ادمین تایید می‌گیریم که واقعا می‌خواد حذف کنه
      const confirmDelete = confirm('مطمئنی می‌خوای این محصول رو حذف کنی؟');

      if (!confirmDelete) {
         return;
      }

      button.disabled = true;

      try {
         const response = await fetch('/shop/frontend/php/admin/products/delete_product.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id: productId })
         });

         const data = await response.json();

         if (data.success) {
            // ردیف مربوط به این محصول رو از جدول حذف کن
            button.closest('tr').remove();
         } else {
            alert(data.message || 'خطا در حذف محصول');
            button.disabled = false;
         }

      } catch (err) {
         alert('خطا در ارتباط با سرور');
         button.disabled = false;
      }
   });
});