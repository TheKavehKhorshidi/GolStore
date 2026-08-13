<?php
require '../../partials/config.php';
?>

<!DOCTYPE html>
<html lang="fa" dir="rtl">

<head>

   <meta charset="UTF-8">

   <meta name="viewport" content="width=device-width, initial-scale=1.0">

   <title>سبد خرید</title>

   <link rel="stylesheet" href="/shop/frontend/css/style.css">

   <link rel="stylesheet" href="/shop/frontend/css/cart/cart.css">

   <link rel="stylesheet" href="/shop/frontend/css/homeStyles/header.css">

</head>

<body>

   <!------------------------------------------------ header ------------------------------------------------>

   <?php include '../../partials/header/header.php'; ?>

   <!------------------------------------------------------------------------------------------------------->


   <main class="container cart-page">

      <!-- Cart Header -->
      <div class="cart-header">

         <h1 class="section-title">
            سبد خرید
         </h1>

         <p class="cart-subtitle">
            محصولاتی که برای خرید انتخاب کردی
         </p>

      </div>


      <!-- Loading -->
      <div id="cart-loading" class="cart-state">

         <div class="spinner"></div>

         <span>
            در حال بارگذاری...
         </span>

      </div>


      <!-- Empty Cart -->
      <div id="cart-empty" class="cart-state" style="display:none;">

         <div class="svgCon">

            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
               stroke="currentColor">
               <path stroke-linecap="round" stroke-linejoin="round"
                  d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 0 0-16.536-1.84M7.5 14.25 5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z" />
            </svg>

         </div>



         <p>
            سبد خرید شما خالی است
         </p>


         <a href="/shop/frontend/index.php" class="btn-outline">
            مشاهده محصولات
         </a>

      </div>


      <!-- Cart Content -->
      <div class="cart-layout">

         <!-- Products -->
         <div id="cart-items" class="cart-items"></div>


         <!-- Summary -->
         <aside id="cart-summary" class="cart-summary" style="display:none;">

            <h2>
               خلاصه سفارش
            </h2>


            <div class="cart-summary-row">

               <span>
                  جمع کل
               </span>

               <strong id="cart-total-price">
                  ۰ تومان
               </strong>

            </div>


            <a href="checkout.php" class="btn-primary">
               ادامه فرآیند خرید
            </a>

         </aside>

      </div>

   </main>


   <!-- Cart JavaScript -->

   <script src="/shop/frontend/js/cart/cart.js"></script>


   <script>

      async function renderCartPage() {

         const loadingEl =
            document.getElementById('cart-loading');

         const emptyEl =
            document.getElementById('cart-empty');

         const itemsEl =
            document.getElementById('cart-items');

         const summaryEl =
            document.getElementById('cart-summary');


         const products =
            await fetchCartProductsInfo();


         loadingEl.style.display = 'none';


         /*
          * Empty Cart
          */

         if (products.length === 0) {

            emptyEl.style.display = 'flex';

            summaryEl.style.display = 'none';

            return;
         }


         /*
          * Cart Has Products
          */

         emptyEl.style.display = 'none';

         itemsEl.style.display = 'flex';


         let total = 0;


         itemsEl.innerHTML = products.map(p => {

            const lineTotal =
               p.price * p.quantity;

            total += lineTotal;


            return `

                    <div
                        class="cart-item"
                        data-id="${p.id}"
                    >

                        <img
                            src="/shop/frontend/${p.image}"
                            alt="${p.name}"
                            class="cart-item-img"
                        >


                        <div class="cart-item-info">

                            <h3>
                                ${p.name}
                            </h3>

                            <p class="cart-item-price">
                                ${p.price.toLocaleString('fa-IR')}
                                تومان
                            </p>

                        </div>


                        <div class="cart-item-qty">

                            <button
                                class="qty-btn"
                                data-action="decrease"
                                data-id="${p.id}"
                                type="button"
                            >
                                −
                            </button>


                            <span>
                                ${p.quantity}
                            </span>


                            <button
                                class="qty-btn"
                                data-action="increase"
                                data-id="${p.id}"
                                type="button"
                            >
                                +
                            </button>

                        </div>


                        <div class="cart-item-total">

                            ${lineTotal.toLocaleString('fa-IR')}
                            تومان

                        </div>


                        <button
                            class="remove-btn"
                            data-id="${p.id}"
                            title="حذف از سبد"
                            type="button"
                        >

                            <svg
                                width="18"
                                height="18"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                            >

                                <path
                                    d="M3 6h18M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2m3 0v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6h14z"
                                />

                            </svg>

                        </button>

                    </div>

                `;

         }).join('');


         /*
          * Total Price
          */

         document
            .getElementById('cart-total-price')
            .textContent =
            total.toLocaleString('fa-IR') + ' تومان';


         summaryEl.style.display = 'block';


         /*
          * Quantity Buttons
          */

         itemsEl
            .querySelectorAll('.qty-btn')
            .forEach(btn => {

               btn.addEventListener('click', () => {

                  const id =
                     parseInt(btn.dataset.id);


                  const cart =
                     getCart();


                  const item =
                     cart.find(i => i.id === id);


                  if (!item) {
                     return;
                  }


                  const newQty =
                     btn.dataset.action === 'increase'
                        ? item.quantity + 1
                        : item.quantity - 1;


                  updateQuantity(
                     id,
                     newQty
                  );


                  renderCartPage();

               });

            });


         /*
          * Remove Buttons
          */

         itemsEl
            .querySelectorAll('.remove-btn')
            .forEach(btn => {

               btn.addEventListener('click', () => {

                  removeFromCart(
                     parseInt(btn.dataset.id)
                  );


                  renderCartPage();

               });

            });

      }


      /*
       * Initial Render
       */

      renderCartPage();

   </script>

</body>

</html>