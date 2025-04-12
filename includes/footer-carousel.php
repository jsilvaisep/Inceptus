<?php
include 'includes/db.php';

$topProducts = $pdo->query("SELECT * FROM PRODUCT ORDER BY PRODUCT_RANK DESC LIMIT 3")->fetchAll(PDO::FETCH_ASSOC);

$topCompanies = $pdo->query("SELECT * FROM COMPANY ORDER BY COMPANY_RANK DESC LIMIT 3")->fetchAll(PDO::FETCH_ASSOC);
?>

<link rel="stylesheet" href="assets/css/footer-carousel.css">

<div class="footer-carousel-wrapper" id="footer-carousel-container">
  <div class="footer-carousel" id="footer-carousel">
    <div class="footer-carousel-slide">
        <div class="footer-carousel-title">Products</div>
      <div class="footer-carousel-cards">
            <?php foreach ($topProducts as $product): ?>
              <a href="?page=produtocompleto&id=<?= $product['PRODUCT_ID'] ?>" class="footer-carousel-card">
                <img src="<?= htmlspecialchars($product['IMG_URL']) ?>" alt="<?= htmlspecialchars($product['PRODUCT_NAME']) ?>">
                <div class="footer-card-content">
                  <h3><?= htmlspecialchars($product['PRODUCT_NAME']) ?></h3>
                  <p><?= mb_strimwidth(htmlspecialchars($product['PRODUCT_DESCRIPTION']), 0, 60, '...') ?></p>
                </div>
              </a>
            <?php endforeach; ?>
      </div>
    </div>

    <div class="footer-carousel-slide">
        <div class="footer-carousel-title">Companies</div>
        <div class="footer-carousel-cards">
        <?php foreach ($topCompanies as $company): ?>
          <a href="?page=empresacompleta&id=<?= $company['COMPANY_ID'] ?>" class="footer-carousel-card">
            <img src="<?= htmlspecialchars($company['IMG_URL']) ?>" alt="<?= htmlspecialchars($company['COMPANY_NAME']) ?>">
            <div class="footer-card-content">
              <h3><?= htmlspecialchars($company['COMPANY_NAME']) ?></h3>
              <p><?= mb_strimwidth(htmlspecialchars($company['COMPANY_DESCRIPTION']), 0, 60, '...') ?></p>
            </div>
          </a>
        <?php endforeach; ?>
      </div>
    </div>

  </div>

  <div class="carousel-dots">
    <span class="dot active" data-slide="0"></span>
    <span class="dot" data-slide="1"></span>
  </div>
</div>

<script>
  document.addEventListener("DOMContentLoaded", () => {
    const carousel = document.getElementById("footer-carousel");
    const slides = document.querySelectorAll(".footer-carousel-slide");
    const dots = document.querySelectorAll(".dot");
    let current = 0;

    function showSlide(index) {
      carousel.style.transform = `translateX(-${index * 100}%)`;
      dots.forEach(dot => dot.classList.remove('active'));
      dots[index].classList.add('active');
    }

    dots.forEach(dot => {
      dot.addEventListener('click', () => {
        current = parseInt(dot.getAttribute('data-slide'));
        showSlide(current);
      });
    });

    setInterval(() => {
      current = (current + 1) % slides.length;
      showSlide(current);
    }, 7000);
  });
</script>
