<?php
include '../includes/db.php';

$search = $_GET['search'] ?? '';
$searchTerm = '%' . $search . '%';
$rank = isset($_GET['rank']) ? (float) $_GET['rank'] : 0;
$type = $_GET['type'] ?? 'both';

$page = isset($_GET['pg']) ? max(1, (int)$_GET['pg']) : 1;
$perPage = 9;
$offset = ($page - 1) * $perPage;

// Total para paginação
$params = [$searchTerm, $rank];
if ($type !== 'both') {
    $query = "SELECT COUNT(*) FROM PRODUCT 
              LEFT JOIN CATEGORY ON PRODUCT.CATEGORY_ID = CATEGORY.CATEGORY_ID 
              WHERE PRODUCT.PRODUCT_NAME LIKE ? 
                AND PRODUCT.PRODUCT_RANK >= ? 
                AND CATEGORY.CATEGORY_TYPE = ?";
    $params[] = $type;
} else {
    $query = "SELECT COUNT(*) FROM PRODUCT 
              WHERE PRODUCT.PRODUCT_NAME LIKE ? 
                AND PRODUCT.PRODUCT_RANK >= ?";
}

$totalStmt = $pdo->prepare($query);
$totalStmt->execute($params);
$totalprodutos = $totalStmt->fetchColumn();
$totalPages = ceil($totalprodutos / $perPage);

// Modal handler
if (isset($_GET['modal']) && isset($_GET['id'])) {
    $productId = $_GET['id'];
    $stmt = $pdo->prepare("SELECT * FROM PRODUCT WHERE PRODUCT_ID = ?");
    $stmt->execute([$productId]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$product) {
        echo '<p>Produto inválido.</p>';
        exit;
    }

    $fullDescription = nl2br(htmlspecialchars($product['PRODUCT_DESCRIPTION']));
    $shortDescription = strlen($fullDescription) > 100 ? substr($fullDescription, 0, 200) . '...' : $fullDescription;
    $showMoreButton = strlen($fullDescription) > 100 ? '<button class="show-more-btn" onclick="toggleDescription(this)">Mais...</button>' : '';
    

    echo '<div class="modal-overlay" onclick="closeModal()"></div>';
    echo '<div class="modal-box fade-in">';
    echo '<button class="modal-close" onclick="closeModal()">&times;</button>';
    echo '<div class="modal-content">';
    echo '<img src="' . htmlspecialchars($product['IMG_URL']) . '" alt="' . htmlspecialchars($product['PRODUCT_NAME']) . '" class="modal-img">';
    echo '<h2>' . htmlspecialchars($product['PRODUCT_NAME']) . '</h2>';
    echo '<div class="product-description">';
    echo '<p class="description-text">' . $shortDescription . '</p>';
    echo '<p class="full-description" style="display:none;">' . $fullDescription . '</p>';
    echo $showMoreButton;
    echo '</div>';
    echo '<p style="color:black;"><strong>Visualizações:</strong> ' . $product['PRODUCT_VIEW_QTY'] . '</p>';
    echo '</div></div>';
    $stmt=null;
    exit;
}
if (isset($_COOKIE['stars'])) {
    echo $_COOKIE["stars"]; 
}

// Total para paginação
$totalStmt = $pdo->prepare("SELECT COUNT(*) FROM PRODUCT WHERE PRODUCT_NAME LIKE ?");
$totalStmt->execute([$searchTerm]);
$totalProducts = $totalStmt->fetchColumn();
$totalPages = ceil($totalProducts / $perPage);

// Produtos paginados
$stmt = $pdo->prepare("SELECT * FROM PRODUCT WHERE PRODUCT_NAME LIKE ? ORDER BY PRODUCT_RANK DESC LIMIT ? OFFSET ?");
$stmt->bindValue(1, $searchTerm);
$stmt->bindValue(2, $perPage, PDO::PARAM_INT);
$stmt->bindValue(3, $offset, PDO::PARAM_INT);
$stmt->execute();
$products = $stmt->fetchAll();
$stmt=null;
?>

<div class="product-layout">

    <!-- Lado esquerdo: Filtros -->
    <div class="filtros">
        <?php include '../includes/filter.php'; ?>
    </div>

    <!-- Lado direito: Produtos -->
    <div class="product-container">
        <h2>Produtos</h2>
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