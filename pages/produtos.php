<?php
include '../includes/db.php';

$search = $_GET['search'] ?? '';
$searchTerm = '%' . $search . '%';
$rank = isset($_GET['rank']) ? (float) $_GET['rank'] : 0;
$type = $_GET['type'] ?? 'both';

$page = isset($_GET['pg']) ? max(1, (int)$_GET['pg']) : 1;
$perPage = 12;
$offset = ($page - 1) * $perPage;

// Modal handler
if (isset($_GET['modal']) && isset($_GET['id'])) {
    $productId = $_GET['id'];
    $stmt = $pdo->prepare("SELECT p.*, c.COMPANY_NAME FROM PRODUCT p INNER JOIN COMPANY c ON c.COMPANY_ID = p.COMPANY_ID WHERE p.PRODUCT_STATUS = 'A' AND p.PRODUCT_ID = ?");
    $stmt->execute([$productId]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$product) {
        echo '<p>Produto inválido.</p>';
        exit;
    }

    $description = substr(nl2br(htmlspecialchars($product['PRODUCT_DESCRIPTION'])), 0, 200);

    echo '<div class="modal-overlay" onclick="closeModal()"></div>';
    echo '<div class="modal-box fade-in">';
    echo '<button class="modal-close" onclick="closeModal()">&times;</button>';
    echo '<div class="modal-content">';
    echo '<img src="' . htmlspecialchars($product['IMG_URL']) . '" alt="' . htmlspecialchars($product['PRODUCT_NAME']) . '" class="modal-img">';
    echo '<h2>' . htmlspecialchars($product['PRODUCT_NAME']) . '</h2>';
    echo '<div class="product-description">';
    echo '<p class="description-text">' . $description . '</p>';
    echo '</div>';
    echo '<p style="color:black;"><strong>Visualizações:</strong> ' . $product['PRODUCT_VIEW_QTY'] . '</p>';
    echo '<p style="color:black;"><strong>Produzido por:</strong> '. $product['COMPANY_NAME'] . '</p>';;
    echo '</div></div>';
    $stmt=null;
    exit;
}

if (isset($_COOKIE['stars'])) {
    echo $_COOKIE["stars"]; 
}

// BASE DA QUERY
$baseQuery = "
    FROM PRODUCT 
    LEFT JOIN CATEGORY ON PRODUCT.CATEGORY_ID = CATEGORY.CATEGORY_ID 
    WHERE PRODUCT.PRODUCT_STATUS = 'A' 
    AND PRODUCT.PRODUCT_NAME LIKE ?
";
$params = [$searchTerm];

// TIPO: produtos / projetos (serviços) / ambos
if ($type === 'products') {
    $baseQuery .= " AND CATEGORY.CATEGORY_TYPE = 'PRODUTO'";
} elseif ($type === 'projects') {
    $baseQuery .= " AND CATEGORY.CATEGORY_TYPE = 'SERVICO'";
}

// RANK
if ($rank > 0) {
    $baseQuery .= " AND PRODUCT.PRODUCT_RANK >= ? AND PRODUCT.PRODUCT_RANK < ?";
    $params[] = $rank;
    $params[] = $rank + 1;
}

// -------- TOTAL --------
$totalStmt = $pdo->prepare("SELECT COUNT(*) " . $baseQuery);
$totalStmt->execute($params);
$totalProducts = $totalStmt->fetchColumn();
$totalPages = ceil($totalProducts / $perPage);

// -------- PRODUTOS PAGINADOS --------
$productsQuery = "SELECT PRODUCT.* " . $baseQuery . " ORDER BY PRODUCT.PRODUCT_RANK DESC LIMIT ? OFFSET ?";
$stmt = $pdo->prepare($productsQuery);

// Bind dos parâmetros normais
for ($i = 0; $i < count($params); $i++) {
    $stmt->bindValue($i + 1, $params[$i]);
}

// Bind dos inteiros LIMIT e OFFSET
$stmt->bindValue(count($params) + 1, $perPage, PDO::PARAM_INT);
$stmt->bindValue(count($params) + 2, $offset, PDO::PARAM_INT);
$stmt->execute();
$products = $stmt->fetchAll();
?>

<div class="produtos-layout">
    <!-- Lado esquerdo: Filtros -->
    <div class="filtros">
        <?php include '../includes/filter.php'; ?>
    </div>

    <!-- Lado direito: Produtos -->
    <div class="product-container">
        <div class="search-section">
            <form class="search-box" data-page="produtos" onsubmit="return false;">
                <span class="search-icon">🔍</span>
                <input type="text" id="search-input" class="search-input" placeholder="Pesquisar produtos...">
                <div id="search-results" class="search-results-box"></div>
            </form>
        </div>

        <?php if (count($products) > 0): ?>
            <div class="product-grid">
                <?php foreach ($products as $product): ?>
                    <?php
                        $img = !empty($product['IMG_URL']) ? htmlspecialchars($product['IMG_URL']) : '/produtos/sem_imagem.png';
                    ?>
                    <div class="product-card clickable-card" data-id="<?= $product['PRODUCT_ID'] ?>">
                        <img src="<?= $img ?>" alt="<?= htmlspecialchars($product['PRODUCT_NAME']) ?>" class="product-img" onerror="this.onerror=null;this.src='/produtos/sem_imagem.png';">
                        <div class="product-info">
                            <h3><?= htmlspecialchars($product['PRODUCT_NAME']) ?></h3>
                            <p><?= htmlspecialchars($product['PRODUCT_DESCRIPTION']) ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="pagination">
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <button class="page-btn<?= $i == $page ? ' active' : '' ?>" onclick="loadPage('produtos', '<?= http_build_query(['search' => $search, 'pg' => $i]) ?>')">
                        <?= $i ?>
                    </button>
                <?php endfor; ?>
            </div>
        <?php else: ?>
            <p class="no-results">Nenhum produto encontrado.</p>
        <?php endif; ?>
    </div>
</div>

<div id="modal-container"></div>