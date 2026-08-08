// header section
fetch('partials/header/header.html')
   .then(res => res.text())
   .then(html => {
      document.getElementById('header-placeholder').innerHTML = html;
   });