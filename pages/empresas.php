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
$totalCompanies = $totalStmt->fetchColumn();
$totalPages = ceil($totalCompanies / $perPage);

// Empresas paginadas
$stmt = $pdo->prepare("SELECT * FROM COMPANY WHERE COMPANY_NAME LIKE ? ORDER BY COMPANY_RANK DESC LIMIT ? OFFSET ?");
$stmt->bindValue(1, $searchTerm);
$stmt->bindValue(2, $perPage, PDO::PARAM_INT);
$stmt->bindValue(3, $offset, PDO::PARAM_INT);
$stmt->execute();
$companies = $stmt->fetchAll();
?>

<div class="company-container">
    <h2>🏢 Empresas</h2>

    <div class="search-section">
        <form method="GET" class="search-box">
            <span class="search-icon" onclick="this.closest('form').requestSubmit()">🔍</span>
            <input type="text" name="search" class="search-input" placeholder="Barra de pesquisa" value="<?= htmlspecialchars($search) ?>">
        </form>
    </div>

    <?php if (count($companies) > 0): ?>
        <div class="company-grid">
            <?php foreach ($companies as $company): ?>
                <div class="company-card">
                    <img src="<?= htmlspecialchars($company['IMG_URL']) ?>" alt="<?= htmlspecialchars($company['COMPANY_NAME']) ?>" class="company-img">
                    <div class="company-info">
                        <h3><?= htmlspecialchars($company['COMPANY_NAME']) ?></h3>
                        <p><?= htmlspecialchars($company['COMPANY_DESCRIPTION']) ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="pagination">
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <button class="page-btn<?= $i == $page ? ' active' : '' ?>" onclick="loadPage('empresas', '<?= http_build_query(['search' => $search, 'pg' => $i]) ?>')">
                <?= $i ?>
                </button>
            <?php endfor; ?>
        </div>
    <?php else: ?>
        <p class="no-results">Nenhuma empresa encontrada.</p>
    <?php endif; ?>
</div>
