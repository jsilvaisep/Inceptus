<?php include '../includes/db.php';
session_start();
if (isset($_SESSION['user'])) {
    $userID = $_SESSION['user']['user_id'];
    $userName = $_SESSION['user']['user_name'] ?? '';
} else {
    header("Location: /pages/redirect.php");
    exit;
}

$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

if ($isAjax && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['resposta'])) {
    $resposta = trim($_POST['comment']);
    $rank = $_POST['commentrank'];
    $userId = $_SESSION['user']['user_id'];

    if (!empty($resposta)) {
        $stmt = $pdo->prepare("CALL INSERT_COMMENT (?, ?, ?, ?, ?)");
        $stmt->execute([$userId, $product['COMPANY_ID'], $product['PRODUCT_ID'], $rank, $resposta]);
    }
}
function renderStars($rating)
{
    $fullStars = floor($rating);
    $halfStar = ($rating - $fullStars) >= 0.5 ? 1 : 0;
    $emptyStars = 5 - ($fullStars + $halfStar);

    $starsHTML = str_repeat('★', $fullStars);
    if ($halfStar)
        $starsHTML .= '☆';
    $starsHTML .= str_repeat('☆', $emptyStars);

    return $starsHTML;
}

$productId = $_GET['id'] ?? '';
if (!$productId) {
    echo '<p>Produto inválido.</p>';
    exit;
}

$stmt = $pdo->prepare("SELECT p.*, c.COMPANY_NAME 
                       FROM PRODUCT p 
                       INNER JOIN COMPANY c ON c.COMPANY_ID = p.COMPANY_ID
                       WHERE p.PRODUCT_ID = ? AND p.PRODUCT_STATUS = 'A'");
$stmt->execute([$productId]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    echo '<p>Produto não encontrado.</p>';
    exit;
}

// Buscar comentários no banco de dados
$commentStmt = $pdo->prepare("SELECT c.COMMENT_TEXT, c.COMMENT_RANK, c.CREATED_AT, u.USER_NAME 
                              FROM COMMENT c 
                              INNER JOIN USER u ON u.USER_ID = c.USER_ID 
                              WHERE c.PRODUCT_ID = ? AND c.COMMENT_STATUS = 'A' 
                              ORDER BY c.CREATED_AT DESC"); // Ordena pelos mais recentes
$commentStmt->execute([$productId]);
$comments = $commentStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="produto-completo-container">
    <!-- Botão "Voltar" -->
    <div class="botao-voltar-modal">
        <a href="?page=produtos" class="botao-voltar">← Voltar</a>
    </div>

    <!-- Cabeçalho do Produto -->
    <div class="produto-header">
        <h1><?= htmlspecialchars($product['PRODUCT_NAME']) ?></h1>
        <p class="product-rating">
            <span><?= renderStars($product['PRODUCT_RANK']) ?></span>
            (<?= htmlspecialchars($product['PRODUCT_RANK']) ?>)
        </p>
    </div>

    <!-- Imagens do Produto -->
    <div class="produto-imagens-container">
        <!-- Imagem Principal -->
        <div class="imagem-principal">
            <img src="<?= htmlspecialchars($product['IMG_URL']) ?>"
                alt="<?= htmlspecialchars($product['PRODUCT_NAME']) ?>" class="imagem-principal-img">
        </div>

        <!-- Imagens Menores -->
        <div class="imagens-secundarias">
            <img src="path/to/image2.jpg" alt="Imagem Secundária 1" class="imagem-secundaria">
            <img src="path/to/image3.jpg" alt="Imagem Secundária 2" class="imagem-secundaria">
        </div>
    </div>

    <!-- Seção de Descrição -->
    <div class="produto-detalhes">
        <h2>Descrição</h2>
        <p><?= nl2br(htmlspecialchars($product['PRODUCT_DESCRIPTION'])) ?></p>
        <p><strong>Visualizações:</strong> <?= $product['PRODUCT_VIEW_QTY'] ?></p>
        <p><strong>Produzido por:</strong> <?= htmlspecialchars($product['COMPANY_NAME']) ?></p>
    </div>

    <!-- Seção de Comentários -->

    <div class="comentarios">
        <form>
            <label for="comment">Comentário</label>
            <textarea id="comment" placeholder="Escreva o seu comentário..." rows="4" style="width:100%;"></textarea>
            <label for="comment_rank">rank</label>
            <input type="comment_rank" name="rank" min="0" max="5">
            <button type="button" onclick="submitComentarioProduto('<?= $product['PRODUCT_ID'] ?>')">Comentar</button>
        </form>
        <h2>Reviews de <?= htmlspecialchars($product['PRODUCT_NAME']) ?></h2>
        <?php if (count($comments) > 0): ?>
            <div class="comentarios-carrossel">
                <?php foreach ($comments as $comment): ?>
                    <div class="comentario-item">
                        <p>"<?= htmlspecialchars($comment['COMMENT_TEXT']) ?>"</p>
                        <p class="product-rating"><?= renderStars($comment['COMMENT_RANK']) ?></p>
                        <footer>- <?= htmlspecialchars($comment['USER_NAME']) ?>, em
                            <?= date('d/m/Y', strtotime($comment['CREATED_AT'])) ?>
                        </footer>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p>Não existem comentários para este produto.</p>
        <?php endif; ?>
    </div>
</div>