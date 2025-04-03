<?php
include '../includes/db.php';
function renderStars($rating) {
    $fullStars = floor($rating);
    $halfStar = ($rating - $fullStars) >= 0.5 ? 1 : 0;
    $emptyStars = 5 - ($fullStars + $halfStar);

    $starsHTML = str_repeat('★', $fullStars);
    if ($halfStar) $starsHTML .= '☆';
    $starsHTML .= str_repeat('☆', $emptyStars);

    return $starsHTML;
}

$productId = $_GET['id'] ?? '';
if (!$productId) {
    echo '<p>Produto inválido.</p>';
    exit;
}

$stmt = $pdo->prepare("SELECT p.*, u.USER_NAME FROM PRODUCT p INNER JOIN COMPANY c ON c.COMPANY_ID = p.COMPANY_ID  INNER JOIN USER u ON u.USER_ID = c.USER_ID  WHERE p.PRODUCT_ID = ? AND p.PRODUCT_STATUS = 'A'");
$stmt->execute([$productId]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    echo '<p>Produto não encontrado.</p>';
    exit;
}
?>

<div class="produto-completo-container">
    <h1><?= htmlspecialchars($product['PRODUCT_NAME']) ?></h1>
    <img src="<?= htmlspecialchars($product['IMG_URL']) ?>" alt="<?= htmlspecialchars($product['PRODUCT_NAME']) ?>" class="produto-completo-img">
    <p><?= renderStars($product['PRODUCT_RANK']) . ' ' . htmlspecialchars($product['PRODUCT_RANK']) ?></p>
    <p><strong>Descrição:</strong></p>
    <p><?= nl2br(htmlspecialchars($product['PRODUCT_DESCRIPTION'])) ?></p>
    <p><strong>Visualizações:</strong> <?= $product['PRODUCT_VIEW_QTY'] ?></p>
    <p><strong>Produzido por:</strong> <?= htmlspecialchars($product['USER_NAME']) ?></p>

    <div class="comentarios-section">
        <h2>Deixe um comentário</h2>
        <form class="comentario-form" method="post" action="processar_comentario.php">
            <textarea name="COMENT_EXT_TEXT" placeholder="Escreva aqui a sua opinião..." required></textarea>
            <input type="hidden" name="COMENT_ID" value="<?php echo uniqid(); ?>">
            <button type="submit">Publicar</button>
        </form>
    </div>
</div>
