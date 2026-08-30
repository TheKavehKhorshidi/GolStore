// _______________________________ جستجو در جدول سفارش‌ها _______________________________

const searchInput = document.getElementById('order-search');
const tableRows = document.querySelectorAll('#orders-table-body tr[data-search-text]');

searchInput.addEventListener('input', () => {
   const query = searchInput.value.trim().toLowerCase();

   tableRows.forEach(row => {
      const searchText = row.dataset.searchText;
      const matches = searchText.includes(query);

      row.classList.toggle('hidden-row', !matches);
   });
});


// _______________________________ دراپ‌داون‌های وضعیت سفارش _______________________________

// برای هر دراپ‌داون وضعیت: هم رنگ اولیه‌ش رو ست می‌کنیم، هم گوش می‌دیم به تغییرش
document.querySelectorAll('.status-select').forEach(select => {

   // رنگ اولیه دراپ‌داون بر اساس همون وضعیتی که الان داره
   select.setAttribute('data-status', select.value);

   // وقتی ادمین وضعیت رو عوض کنه، این بخش اجرا میشه
   select.addEventListener('change', async () => {
      const orderId = select.dataset.orderId;
      const newStatus = select.value;
      const previousStatus = select.dataset.status;

      select.disabled = true;

      try {
         const response = await fetch('/shop/frontend/php/admin/update_status.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ order_id: orderId, status: newStatus })
         });

         const data = await response.json();

         if (!data.success) {
            alert(data.message || 'خطا در تغییر وضعیت');
            select.value = previousStatus; // برگردون به وضعیت قبلی
         } else {
            select.setAttribute('data-status', newStatus); // رنگ رو آپدیت کن
         }

      } catch (err) {
         alert('خطا در ارتباط با سرور');
         select.value = previousStatus;
      } finally {
         select.disabled = false;
      }
   });
});