<?php
include '../includes/db.php';
session_start();

// Funções auxiliares
function renderStars($rating) {
    $fullStars = floor($rating);
    $halfStar = ($rating - $fullStars) >= 0.5 ? 1 : 0;
    $emptyStars = 5 - ($fullStars + $halfStar);

    $starsHTML = str_repeat('★', $fullStars);
    if ($halfStar) $starsHTML .= '☆';
    $starsHTML .= str_repeat('☆', $emptyStars);

    return $starsHTML;
}

// Parâmetros GET com valores padrão
$search = $_GET['search'] ?? '';
$searchTerm = '%' . $search . '%';
$rank = isset($_GET['rank']) ? (float) $_GET['rank'] : 0;
$minViews = isset($_GET['min_views']) ? (int) $_GET['min_views'] : null;
$maxViews = isset($_GET['max_views']) ? (int) $_GET['max_views'] : null;
$page = isset($_GET['pg']) ? max(1, (int)$_GET['pg']) : 1;
$perPage = 12;
$offset = ($page - 1) * $perPage;
$tags = isset($_GET['tags']) ? array_filter(array_map('trim', explode(',', $_GET['tags']))) : []; // array de strings

// Base da query
$baseQuery = "FROM COMPANY 
                LEFT JOIN TAG_COMPANY ON COMPANY.COMPANY_ID = TAG_COMPANY.COMPANY_ID
                LEFT JOIN TAG ON TAG_COMPANY.TAG_ID = TAG.TAG_ID
                WHERE COMPANY.COMPANY_STATUS = 'A' AND COMPANY.COMPANY_NAME LIKE ?";

$params = [$searchTerm];

// Filtros de visualizações
if (!is_null($minViews) && !is_null($maxViews) && $minViews > 0 && $maxViews > $minViews) {
    $baseQuery .= " AND COMPANY.COMPANY_VIEW_QTY BETWEEN ? AND ?";
    $params[] = $minViews;
    $params[] = $maxViews;
}

// Filtro de rank
if ($rank > 0) {
    $baseQuery .= " AND COMPANY.COMPANY_RANK >= ? AND COMPANY.COMPANY_RANK < ?";
    $params[] = $rank;
    $params[] = $rank + 1;
}

// Filtro de tags
if ($tags && count($tags) > 0) {
    $baseQuery .= " AND EXISTS (
                    SELECT 1 
                    FROM TAG_COMPANY tc2 
                    INNER JOIN TAG t2 ON tc2.TAG_ID = t2.TAG_ID 
                    WHERE tc2.COMPANY_ID = COMPANY.COMPANY_ID 
                      AND t2.TAG_NAME IN (" . implode(',', array_fill(0, count($tags), '?')) . ")
                )";
    $params = array_merge($params, $tags);
}

// Total de empresas
$totalStmt = $pdo->prepare("SELECT COUNT(*) " . $baseQuery);
$totalStmt->execute($params);
$totalCompanies = $totalStmt->fetchColumn();
$totalPages = ceil($totalCompanies / $perPage);

// Empresas paginadas
$companiesQuery = "SELECT COMPANY.* " . $baseQuery . " ORDER BY COMPANY.COMPANY_RANK DESC LIMIT ? OFFSET ?";
$stmt = $pdo->prepare($companiesQuery);

foreach ($params as $i => $param) {
    $stmt->bindValue($i + 1, $param);
}
$stmt->bindValue(count($params) + 1, $perPage, PDO::PARAM_INT);
$stmt->bindValue(count($params) + 2, $offset, PDO::PARAM_INT);

$stmt->execute();
$companies = $stmt->fetchAll();
?>

<link rel="stylesheet" href="../css/empresas.css">

<div class="company-layout">
    <!-- Filtros -->
    <div class="filtros">
        <?php include '../includes/filter.php'; ?>
    </div>

    <!-- Conteúdo principal -->
    <div class="company-container">
        <!-- Lista de empresas -->
        <?php if (count($companies) > 0): ?>
            <div class="company-grid">
                <?php foreach ($companies as $company):
                    $companyId = urlencode($company['COMPANY_ID']); ?>
                    <div class="company-card clickable-card" data-id="<?= $companyId ?>" onclick="redirectToCompany('<?= $companyId ?>')">
                        <img src="<?= htmlspecialchars($company['IMG_URL']) ?>" alt="<?= htmlspecialchars($company['COMPANY_NAME']) ?>" class="company-img">
                        <div class="company-info">
                            <h3><?= htmlspecialchars($company['COMPANY_NAME']) ?></h3>
                            <p><?= htmlspecialchars($company['COMPANY_DESCRIPTION']) ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Paginação -->
            <div class="pagination">
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <button class="page-btn<?= $i == $page ? ' active' : '' ?>" 
                        onclick="loadPage('empresas', '<?= http_build_query(['search' => $search, 'pg' => $i]) ?>')">
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