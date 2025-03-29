<?php
include '../includes/db.php';

$search = $_GET['search'] ?? '';
$searchTerm = '%' . $search . '%';
$rank = isset($_GET['rank']) ? (float) $_GET['rank'] : 0;

$page = isset($_GET['pg']) ? max(1, (int)$_GET['pg']) : 1;
$perPage = 9;
$offset = ($page - 1) * $perPage;

// Total para paginação
$totalStmt = $pdo->prepare("SELECT COUNT(*) FROM PRODUCT WHERE PRODUCT_NAME LIKE ?");
$totalStmt->execute([$searchTerm]);
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

<div class="produtos-layout">

    <!-- Lado esquerdo: Filtros -->
    <div class="filtros">
        <?php include '../includes/filter.php'; ?>
    </div>

    <!-- Lado direito: Produtos -->
    <div class="produtos-conteudo">
        <div class="company-container">
            <h2>Produtos</h2>

            <div class="search-section">
                <form class="search-box" data-page="produtos" onsubmit="return false;">
                    <span class="search-icon">🔍</span>
                    <input type="text" id="search-input" class="search-input" placeholder="Pesquisar produtos...">
                    <div id="search-results" class="search-results-box"></div>
                </form>
            </div>

            <?php if (count($products) > 0): ?>
                <div class="company-grid">
                    <?php foreach ($products as $product): ?>
                        <?php
                            $img = !empty($product['IMG_URL']) ? htmlspecialchars($product['IMG_URL']) : '/produtos/sem_imagem.png';
                        ?>
                        <div class="company-card clickable-card" data-id="<?= $product['PRODUCT_ID'] ?>">
                            <img src="<?= $img ?>" alt="<?= htmlspecialchars($product['PRODUCT_NAME']) ?>" class="company-img" onerror="this.onerror=null;this.src='/produtos/sem_imagem.png';">
                            <div class="company-info">
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
</div>

<div id="modal-container"></div>


<style>
.modal-overlay {
    position: fixed;
    top: 0; left: 0;
    width: 100vw;
    height: 100vh;
    background: rgba(0, 0, 0, 0.5);
    z-index: 999;
}
.modal-box {
    position: fixed;
    top: 50%; left: 50%;
    transform: translate(-50%, -50%);
    background: #fff;
    padding: 30px;
    border-radius: 16px;
    width: 90%;
    max-width: 600px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
    z-index: 1000;
    animation: fadeIn 0.3s ease;
}
.modal-close {
    position: absolute;
    top: 10px;
    right: 15px;
    font-size: 24px;
    border: none;
    background: none;
    cursor: pointer;
    color: #be3144;
}
.modal-content img.modal-img {
    width: 100%;
    height: auto;
    border-radius: 12px;
    margin-bottom: 15px;
}
.modal-content h2 {
    margin: 0 0 10px;
    color: #be3144;
    font-size: 24px;
}

.produtos-layout {
    display: flex;
    gap: 20px;
    padding: 20px;
}

.filtros {
    flex: 0 0 20%;
    max-width: 20%;
}

.produtos-conteudo {
    flex: 1;
    max-width: 80%;
}

@media (max-width: 768px) {
    .produtos-layout {
        flex-direction: column;
    }

    .filtros, .produtos-conteudo {
        max-width: 100%;
        flex: 1 1 100%;
    }
}


@keyframes fadeIn {
    from { opacity: 0; transform: translate(-50%, -60%); }
    to { opacity: 1; transform: translate(-50%, -50%); }
}

.product-description {
    position: relative;
    margin-bottom: 15px;
}

.show-more-btn {
    background: #bd283d;
    color: white;
    border: 10px;
    padding: 8px 15px;
    border-radius: 20px;
    cursor: pointer;
    margin-top: 5px;
    transition: background 0.3s;
}

.show-more-btn:hover {
    background: #9a2533;
}

.description-text, .full-description {
    word-wrap: break-word;
</style>

<script>
    
</script>
