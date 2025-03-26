<?php
include '../includes/db.php';

$search = $_GET['search'] ?? '';
$searchTerm = '%' . $search . '%';

$page = isset($_GET['pg']) ? max(1, (int)$_GET['pg']) : 1;
$perPage = 9;
$offset = ($page - 1) * $perPage;

// Total para paginação
$totalStmt = $pdo->prepare("SELECT COUNT(*) FROM COMPANY WHERE COMPANY_NAME LIKE ?");
$totalStmt->execute([$searchTerm]);
$totalprodutos = $totalStmt->fetchColumn();
$totalPages = ceil($totalprodutos / $perPage);

// Empresas paginadas
$stmt = $pdo->prepare("SELECT * FROM PRODUCT WHERE PRODUCT_NAME LIKE ? ORDER BY PRODUCT_RANK DESC LIMIT ? OFFSET ?");
$stmt->bindValue(1, $searchTerm);
$stmt->bindValue(2, $perPage, PDO::PARAM_INT);
$stmt->bindValue(3, $offset, PDO::PARAM_INT);
$stmt->execute();
$products = $stmt->fetchAll();
?>

<div class="company-container">
    <h2>Produtos</h2>

    <div class="search-section">
        <form class="search-box" data-page="produtos">
            <span class="search-icon" onclick="this.closest('form').requestSubmit()">🔍</span>
            <input type="text" name="search" class="search-input" placeholder="Barra de pesquisa" value="<?= htmlspecialchars($search) ?>">
        </form>
    </div>

    <?php if (count($products) > 0): ?>
        <div class="company-grid">
            <?php foreach ($products as $products): ?>
                <div class="company-card">
                    <img src="<?= htmlspecialchars($products['IMG_URL']) ?>" alt="<?= htmlspecialchars($products['PRODUCT_NAME']) ?>" class="company-img">
                    <div class="company-info">
                        <h3><?= htmlspecialchars($products['PRODUCT_NAME']) ?></h3>
                        <p><?= htmlspecialchars($products['PRODUCT_DESCRIPTION']) ?></p>
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
