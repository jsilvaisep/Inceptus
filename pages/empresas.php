<?php
include '../includes/db.php';

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

// Modal de empresa
if (isset($_GET['modal']) && isset($_GET['id'])) {
    $companyId = $_GET['id'];
    $stmt = $pdo->prepare("SELECT * FROM COMPANY 
                                WHERE COMPANY_STATUS = 'A' AND COMPANY_ID = ?");
    $stmt->execute([$companyId]);
    $company = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$company) {
        echo '<p>Empresa inválida.</p>';
        exit;
    }

    $siteUrl = htmlspecialchars($company['COMPANY_SITE']);
    if (!preg_match("~^(?:f|ht)tps?://~i", $siteUrl)) {
        $siteUrl = "https://" . $siteUrl;
    }

    echo '<link rel="stylesheet" href="../css/empresas.css">';
    echo '<div class="modal-overlay" onclick="closeModal()"></div>';
    echo '<div class="modal-box show">';
    echo '<button class="modal-close" onclick="closeModal()">&times;</button>';
    echo '<div class="modal-content">';
    echo '<img src="' . htmlspecialchars($company['IMG_URL']) . '" alt="' . htmlspecialchars($company['COMPANY_NAME']) . '" class="modal-img">';

    echo '<div class="modal-header">';
    echo '<div><h2>' . htmlspecialchars($company['COMPANY_NAME']) . '</h2></div>';
    echo '<div class="header-actions">';
    echo '<a href="' . $siteUrl . '" class="visit-button" target="_blank" rel="noopener noreferrer">Visite</a>';
    echo '</div></div>';

    echo '<div class="company-rating">';
    echo '<p>' . renderStars($company['COMPANY_RANK']) . htmlspecialchars($company['COMPANY_RANK']) . '</p>';
    echo '</div>';

    echo '<div class="company-description">';
    echo '<p>' . nl2br(htmlspecialchars($company['COMPANY_DESCRIPTION'])) . '</p>';
    echo '</div>';

    echo '<p class="products-title"><strong>Produtos lançados pela ' . htmlspecialchars($company['COMPANY_NAME']) . ':</strong></p>';

    // Selecionar produtos reais da BD (para já com imagens mock locais e link vazio)
    $sql = "SELECT PRODUCT_ID, PRODUCT_NAME, IMG_URL FROM PRODUCT WHERE COMPANY_ID = ? LIMIT 3";
    $productStmt = $pdo->prepare($sql);
    $productStmt->execute([$companyId]);
    $products = $productStmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($products) > 0) {
        echo '<div class="product-list">';
        foreach ($products as $product) {
            echo '<div class="product-item">';
            echo '<a href="">';
            echo '<img src="' . htmlspecialchars($product['IMG_URL']) . '" alt="' . htmlspecialchars($product['PRODUCT_NAME']) . '">';
            echo '</a>';
            echo '</div>';
        }
        echo '</div>';
    } else {
        echo '<p>Esta empresa ainda não lançou produtos.</p>';
    }

    echo '</div></div>';
    $stmt = null;
    exit;
}

if (isset($_COOKIE['stars'])) {
    echo $_COOKIE["stars"];
}

$baseQuery = "FROM COMPANY 
                WHERE COMPANY.COMPANY_STATUS = 'A' AND COMPANY.COMPANY_NAME LIKE ?";
$params = [$searchTerm];

if (!is_null($minViews) && !is_null($maxViews) && $minViews > 0 && $maxViews > $minViews) {
    $baseQuery .= " AND COMPANY.COMPANY_VIEW_QTY BETWEEN ? AND ?";
    $params[] = $minViews;
    $params[] = $maxViews;
}

if ($rank > 0) {
    $baseQuery .= " AND COMPANY.COMPANY_RANK >= ? AND COMPANY.COMPANY_RANK < ?";
    $params[] = $rank;
    $params[] = $rank + 1;
}

$totalStmt = $pdo->prepare("SELECT COUNT(*) " . $baseQuery);
$totalStmt->execute($params);
$totalCompanies = $totalStmt->fetchColumn();
$totalPages = ceil($totalCompanies / $perPage);

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
    <div class="filtros">
        <?php include '../includes/filter.php'; ?>
    </div>

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