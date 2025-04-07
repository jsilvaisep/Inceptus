<?php
include '../includes/db.php';
session_start();

if (isset($_SESSION['user'])) {
    $userID = $_SESSION['user']['user_id'];
    $userName = $_SESSION['user']['user_name'] ?? '';
} else {
    header("Location: /pages/redirect.php");
    exit;
}

$companyId = $_GET['id'] ?? '';
if (!$companyId) {
    echo '<p>Empresa inválida.</p>';
    exit;
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

// Buscar os detalhes da empresa
$stmt = $pdo->prepare("SELECT c.* 
                       FROM COMPANY c
                       WHERE c.COMPANY_ID = ? AND c.COMPANY_STATUS = 'A'");
$stmt->execute([$companyId]);
$company = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$company) {
    echo '<p>Empresa não encontrada.</p>';
    exit;
}

// Noticias na bd
$postStmt = $pdo->prepare("SELECT POST_ID, POST_CONTENT, CREATED_AT 
                           FROM POST
                           WHERE COMPANY_ID = ? AND POST_STATUS = 'A'
                           ORDER BY CREATED_AT DESC"); // Ordena pelas mais recentes
$postStmt->execute([$companyId]);
$posts = $postStmt->fetchAll(PDO::FETCH_ASSOC);

// Produtos lançados pela empresa
$productStmt = $pdo->prepare("SELECT PRODUCT_ID, PRODUCT_NAME, CATEGORY_ID, PRODUCT_RANK, PRODUCT_VIEW_QTY, IMG_URL, CREATED_AT
                              FROM PRODUCT
                              WHERE COMPANY_ID = ? AND PRODUCT_STATUS = 'A'
                              ORDER BY CREATED_AT DESC");
$productStmt->execute([$companyId]);
$products = $productStmt->fetchAll(PDO::FETCH_ASSOC);


?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($company['COMPANY_NAME']) ?> - Notícias</title>
    <link rel="stylesheet" href="assets/css/empresacompleta.css">
</head>

<body>
    <div class="empresa-completa-container">
        <!-- Botão "Voltar" -->
        <div class="botao-voltar-modal">
            <a href="?page=empresas" class="botao-voltar">← Voltar</a>
        </div>

        <!-- Cabeçalho da Empresa -->
        <div class="empresa-header">
            <?php if (!empty($company['LOGO_URL'])): ?>
                <img src="<?= htmlspecialchars($company['LOGO_URL']) ?>"
                    alt="Logo de <?= htmlspecialchars($company['COMPANY_NAME']) ?>">
            <?php endif; ?>
            <h1><?= htmlspecialchars($company['COMPANY_NAME']) ?></h1>
            <p class="empresa-rating">
                <span><?= renderStars($company['COMPANY_RANK']) ?></span>
                (<?= htmlspecialchars($company['COMPANY_RANK']) ?>)
            </p>
        </div>

        <!-- Container Flexível para Imagem e Descrição -->
        <div class="empresa-informacoes">
            <!-- Imagem Principal -->
            <div class="imagem-principal">
                <img src="<?= htmlspecialchars($company['IMG_URL']) ?>"
                    alt="<?= htmlspecialchars($company['COMPANY_NAME']) ?>" class="imagem-principal-img">
            </div>

            <!-- Descrição -->
            <div class="empresa-detalhes">
                <h2>Descrição</h2>
                <p><?= nl2br(htmlspecialchars($company['COMPANY_DESCRIPTION'])) ?></p>
                <p><strong>Visualizações:</strong> <?= $company['COMPANY_VIEW_QTY'] ?></p>
                <p><strong>Produzido por:</strong> <?= htmlspecialchars($company['COMPANY_NAME']) ?></p>
            </div>
        </div>

        <!-- Seção de Produtos Lançados -->
        <div class="produtos">
            <h2>Produtos Lançados por <?= htmlspecialchars($company['COMPANY_NAME']) ?></h2>
            <div class="produtos-carrossel">
                <?php foreach ($products as $product): ?>
                    <div class="produto-item">
                        <!-- Link para a Página do Produto ao Clicar na Imagem -->
                        <a href="?page=produtocompleto&id=<?= urlencode($product['PRODUCT_ID']) ?>">
                            <img src="<?= htmlspecialchars($product['IMG_URL']) ?>"
                                alt="<?= htmlspecialchars($product['PRODUCT_NAME']) ?>" class="produto-img">
                        </a>
                        <h3><?= htmlspecialchars($product['PRODUCT_NAME']) ?></h3>
                        <p><strong>Rating:</strong> <?= renderStars($product['PRODUCT_RANK']) ?>
                            (<?= htmlspecialchars($product['PRODUCT_RANK']) ?>)</p>
                        <p><strong>Visualizações:</strong> <?= htmlspecialchars($product['PRODUCT_VIEW_QTY']) ?></p>
                    </div>
                <?php endforeach; ?>
                <?php if (count($products) == 0): ?>
                    <div>Esta empresa ainda não lançou nenhum produto.</div>
                <?php endif; ?>
            </div>
        </div>

        <div class="noticias">
            <h2>Notícias de <?= htmlspecialchars($company['COMPANY_NAME']) ?></h2>
            <?php if (count($posts) > 0): ?>
                <div class="noticias-lista">
                    <?php foreach ($posts as $post): ?>
                        <a href="?page=noticiacompleta&id=<?= urlencode($post['POST_ID']) ?>">
                            <p><strong>Publicado em:</strong>
                                <?= htmlspecialchars(date("d/m/Y H:i", strtotime($post['CREATED_AT']))) ?></p>
                            <p><?= nl2br(htmlspecialchars($post['POST_CONTENT'])) ?></p>
                        </a>
                        <hr>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p>Não há notícias disponíveis para esta empresa no momento.</p>
            <?php endif; ?>
        </div>
    </div>
</body>