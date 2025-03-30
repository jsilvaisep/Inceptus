<?php
require_once 'db.php';

if (!isset($_GET['q'])) {
    echo '';
    exit;
}

$search = trim($_GET['q']);
$term = "%$search%";

$stmt = $pdo->prepare("SELECT PRODUCT_ID, PRODUCT_NAME, PRODUCT_DESCRIPTION, IMG_URL FROM PRODUCT WHERE PRODUCT_NAME LIKE ? LIMIT 10");
$stmt->execute(params: [$term]);
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (count($results) === 0) {
    echo '<p class="no-results">Nenhum produto encontrado.</p>';
    exit;
}

foreach ($results as $produto) {
    echo '<div class="search-result-item clickable-product" data-id="' . $produto['PRODUCT_ID'] . '">';
    echo '<img class="product-img" src="' . htmlspecialchars($produto['IMG_URL']) . '" alt="Imagem do Produto">';
    echo '<div class="info">';
    echo '<strong>' . htmlspecialchars($produto['PRODUCT_NAME']) . '</strong>';
    echo '<p>' . htmlspecialchars($produto['PRODUCT_DESCRIPTION']) . '</p>';
    echo '</div></div>';
}
?>
