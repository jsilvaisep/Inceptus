<?php
include '../includes/db.php';
?>

<link rel="stylesheet" href="assets/css/banner-news.css">

<?php
$stmt = $pdo->query("SELECT p.*, c.COMPANY_NAME, c.IMG_URL 
    FROM POST p
    INNER JOIN COMPANY c ON p.COMPANY_ID = c.COMPANY_ID 
    WHERE p.POST_STATUS = 'A'
    ORDER BY p.CREATED_AT DESC 
    LIMIT 10
");
$news = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="news-carousel-container">
    <div class="news-carousel" id="news-carousel">
        <?php foreach ($news as $index => $item): ?>
            <div class="news-slide<?= $index === 0 ? ' active' : '' ?>"
                data-title="<?= htmlspecialchars($item['COMPANY_NAME']) ?>"
                data-img="<?= htmlspecialchars($item['IMG_URL']) ?>"
                data-content="<?= htmlspecialchars($item['POST_CONTENT']) ?>"
                data-date="<?= date('d/m/Y', strtotime($item['CREATED_AT'])) ?>">

                <div class="news-banner">
                    <div class="banner-left">
                        <img src="<?= htmlspecialchars($item['IMG_URL']) ?>" alt="Imagem" class="banner-img">
                    </div>
                    <div class="banner-center">
                        <h3 class="banner-title"><?= htmlspecialchars($item['COMPANY_NAME']) ?></h3>
                        <p class="banner-text"><?= nl2br(htmlspecialchars(mb_strimwidth($item['POST_CONTENT'], 0, 100, '...'))) ?></p>
                        <span class="news-date">🕒 <?= date('d/m/Y', strtotime($item['CREATED_AT'])) ?></span>
                    </div>
                    <div class="banner-right">
                        <a href="?page=empresacompleta&id=<?= htmlspecialchars($item['COMPANY_ID']) ?>" class="read-more-btn">SABER MAIS</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
