<?php
include '../includes/db.php';

$search = $_GET['search'] ?? '';
$searchTerm = '%' . $search . '%';

$page = isset($_GET['pg']) ? max(1, (int)$_GET['pg']) : 1;
$perPage = 9;
$offset = ($page - 1) * $perPage;

// Modal handler
if (isset($_GET['modal']) && isset($_GET['id'])) {
    $companyId = $_GET['id'];
    $stmt = $pdo->prepare("SELECT * FROM COMPANY WHERE COMPANY_ID = ?");
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
    exit;
}

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
    
    <div class="search-section">
        <form class="search-box" data-page="empresas">
            <span class="search-icon" onclick="this.closest('form').requestSubmit()">🔍</span>
            <input type="text" name="search" class="search-input" placeholder="Barra de pesquisa" value="<?= htmlspecialchars($search) ?>">
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

<div id="modal-container"></div>

<style>
.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;
    background-color: rgba(0,0,0,0.5);
    z-index: 1000;
}

.modal-box {
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%) scale(0.95);
    background: #fff;
    border-radius: 16px;
    padding: 20px;
    width: 90%;
    max-width: 600px;
    z-index: 1001;
    box-shadow: 0 15px 30px rgba(0,0,0,0.2);
    opacity: 0;
    transition: all 0.3s ease;
}

.modal-box.show {
    opacity: 1;
    transform: translate(-50%, -50%) scale(1);
}

.modal-close {
    background: transparent;
    border: none;
    font-size: 28px;
    color: #be3144;
    position: absolute;
    top: 10px;
    right: 20px;
    cursor: pointer;
}

.modal-content {
    padding-top: 30px;
    text-align: center;
}

.modal-img {
    width: 100%;
    max-height: 250px;
    object-fit: contain;
    margin-bottom: 20px;
    border-radius: 12px;
}

body.no-scroll {
    overflow: hidden;
}
</style>

<script>
document.querySelectorAll('.clickable-card').forEach(card => {
    card.addEventListener('click', () => {
        const id = card.dataset.id;
        fetch('pages/empresas.php?id=' + id + '&modal=true')
            .then(res => res.text())
            .then(html => {
                document.getElementById('modal-container').innerHTML = html;
                document.body.classList.add('no-scroll');
            });
    });
});

function closeModal() {
    document.getElementById('modal-container').innerHTML = '';
    document.body.classList.remove('no-scroll');
}
</script>
