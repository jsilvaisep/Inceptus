<?php
include '../includes/db.php';
session_start();

// Verificar se o usuário está logado
if (isset($_SESSION['user'])) {
    $userID = $_SESSION['user']['user_id'];
    $userName = $_SESSION['user']['user_name'] ?? '';
} else {
    header("Location: /pages/redirect.php");
    exit;
}

//Valida conexão à BD
try {
    if (!isset($pdo) && isset($conn)) {
        $pdo = $conn;
    } else if (!isset($pdo)) {
        throw new Exception("Conexão com a BD não encontrada");
    }
} catch (Exception $e) {
    die("Erro na conexão com a BD: " . $e->getMessage());
}

// Verificar se a requisição é AJAX e se é um POST
$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

if ($isAjax && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['resposta']) && isset($_POST['rank'])) {
    $resposta = trim($_POST['resposta']);
    $rank = $_POST['rank'];
    $productId = $_GET['id'] ?? '';

    // Recuperar o produto da base de dados
    $stmt = $pdo->prepare("SELECT * FROM PRODUCT WHERE PRODUCT_ID = ?");
    $stmt->execute([$productId]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$product) {
        echo 'Produto não encontrado.';
        exit;
    }

    $company_Id = $product['COMPANY_ID'] ?? '';

    // Inserir comentário via stored procedure
    if (!empty($resposta) && !empty($rank)) {
        $stmt = $pdo->prepare("CALL INSERT_COMMENT (?, ?, ?, ?, ?)");
        $stmt->execute([$userID, $company_Id, $productId, $rank, $resposta]);
        echo 'Comentário inserido com sucesso!';
    } else {
        echo 'Por favor, preencha o comentário e a classificação.';
    }
    exit;
}

// Recuperar o ID do produto da URL
$productId = $_GET['id'] ?? '';
if (empty($productId)) {
    echo '<p>Produto inválido.</p>';
    exit;
}

// Recuperar informações do produto
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

// Incrementar contagem de visualizações (apenas uma vez por sessão por produto)
$viewKey = 'viewed_product_' . $productId;
$timeKey = 'viewed_time_' . $productId;
$now = time();
$expireTime = 30; // Aguarda 30 segundos na session para voltar a incrementar

// Verificar se já visualizou e se o tempo já expirou
if (!isset($_SESSION[$viewKey]) ||
    (isset($_SESSION[$timeKey]) && ($now - $_SESSION[$timeKey]) > $expireTime)) {

    try {
        $updateStmt = $pdo->prepare("UPDATE PRODUCT SET PRODUCT_VIEW_QTY = PRODUCT_VIEW_QTY + 1 WHERE PRODUCT_ID = ?");
        $updateStmt->execute([$productId]);

        // Atualizar o valor em $product para exibição correta
        $product['PRODUCT_VIEW_QTY']++;

        // Marcar o produto como visualizado nesta sessão
        $_SESSION[$viewKey] = true;
        $_SESSION[$timeKey] = $now;
    } catch (PDOException $e) {
        error_log("Erro ao atualizar visualizações: " . $e->getMessage());
    }
}

// Buscar comentários no banco de dados
$commentStmt = $pdo->prepare("SELECT c.COMMENT_TEXT, c.COMMENT_RANK, c.CREATED_AT, u.USER_NAME
                              FROM COMMENT c
                              INNER JOIN USER u ON u.USER_ID = c.USER_ID
                              WHERE c.PRODUCT_ID = ? AND c.COMMENT_STATUS = 'A'
                              ORDER BY c.CREATED_AT DESC
                              LIMIT 10");
$commentStmt->execute([$productId]);
$comments = $commentStmt->fetchAll(PDO::FETCH_ASSOC);

// Função para renderizar as estrelas
function renderStars($rating)
{
    $rating = max(0, min(5, $rating)); // Limita o rating entre 0 e 5
    $fullStars = floor($rating);
    $halfStar = ($rating - $fullStars) >= 0.5 ? 1 : 0;
    $emptyStars = 5 - ($fullStars + $halfStar);

    $starsHTML = str_repeat('★', $fullStars);
    if ($halfStar)
        $starsHTML .= '☆';
    $starsHTML .= str_repeat('☆', $emptyStars);

    return $starsHTML;
}
?>


<div class="produto-completo-container">
    <!-- Botão "Voltar" -->
    <div class="botao-voltar-modal">
        <button class="botao-voltar" onclick="redirectToProductsPage()">Voltar</button>
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
        <p><strong>Visualizações:</strong>
            <?php
            if ($product['PRODUCT_VIEW_QTY'] == 0) {
                echo 'O produto ainda não tem visualizações.';
            } else {
                echo htmlspecialchars($product['PRODUCT_VIEW_QTY']);
            }
            ?>
        </p>
        <p><strong>Produzido por:</strong> <?= htmlspecialchars($product['COMPANY_NAME']) ?></p>
    </div>

    <!-- Seção de Comentários -->
    <div class="comentarios">
        <form>
            <div class="comment_form">
                <h2>Comentário</h2>
                <textarea id="comment" placeholder="Escreva o seu comentário..." rows="4"
                          style="width:100%;"></textarea>
                <h4>Rank</h4>
                <div class="comment_rank">
                    <input type="number" id="review" min="0" max="5">
                    <button class="botao-voltar" type="button"
                            onclick="submitComentarioProduto('<?= $product['PRODUCT_ID'] ?>')">Comentar</button>
                </div>
            </div>
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