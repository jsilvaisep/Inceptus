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
    $companyId = $_GET['id'];
    $stmt = $pdo->prepare("SELECT * FROM COMPANY WHERE COMPANY_STATUS = 'A' AND COMPANY_ID = ?");
    $stmt->execute([$companyId]);
    $company = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$company) {
        echo '<p>Empresa inválida.</p>';
        exit;
    }

    echo '<div class="modal-overlay" onclick="closeModal()"></div>';
    echo '<div class="modal-box show">';
    echo '<button class="modal-close" onclick="closeModal()">&times;</button>';
    echo '<div class="modal-content">';
    echo '<img src="' . htmlspecialchars($company['IMG_URL']) . '" alt="' . htmlspecialchars($company['COMPANY_NAME']) . '" class="modal-img">';
    echo '<h2>' . htmlspecialchars($company['COMPANY_NAME']) . '</h2>';
    echo '<p>' . nl2br(htmlspecialchars($company['COMPANY_DESCRIPTION'])) . '</p>';
    echo '</div></div>';
    $stmt=null;
    exit;
}

// Total para paginação
$totalStmt = $pdo->prepare("SELECT COUNT(*) FROM COMPANY WHERE COMPANY_STATUS = 'A'");
$totalStmt->execute();
$totalCompanies = $totalStmt->fetchColumn();
$totalPages = ceil($totalCompanies / $perPage);

// Empresas paginadas
$stmt = $pdo->prepare("SELECT * FROM COMPANY WHERE COMPANY_STATUS = 'A' AND COMPANY_NAME LIKE ? ORDER BY COMPANY_RANK DESC LIMIT ? OFFSET ?");
$stmt->bindValue(1, $searchTerm);
$stmt->bindValue(2, $perPage, PDO::PARAM_INT);
$stmt->bindValue(3, $offset, PDO::PARAM_INT);
$stmt->execute();
$companies = $stmt->fetchAll();
$stmt=null;
?>

<div class="company-layout">    
    <!-- Lado esquerdo: Filtros -->
    <div class="filtros">
        <?php include '../includes/filter.php'; ?>
    </div>
            
    <!-- Lado direito: Produtos -->
    <div class="company-container">
        <div class="search-section">
            <form class="search-box" data-page="empresas" onsubmit="return false;">
                <span class="search-icon">🔍</span>
                <input type="text" id="search-input" class="search-input" placeholder="Pesquisar empresas...">
                <div id="search-results" class="search-results-box"></div>
            </form>
        </div>

        <?php if (count($companies) > 0): ?>
            <div class="company-grid">
                <?php foreach ($companies as $company): ?>
                    <div class="company-card clickable-card" data-id="<?= $company['COMPANY_ID'] ?>">
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
</div>

<div id="modal-container"></div>