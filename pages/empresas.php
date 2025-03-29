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
    $stmt=null;
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
$stmt=null;
?>

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
    function closeModal() {
        document.getElementById('modal-container').innerHTML = '';
    }
    function toggleDescription(button) {
        const container = button.closest('.product-description');
        const shortText = container.querySelector('.description-text');
        const fullText = container.querySelector('.full-description');

        if (fullText.style.display === 'none') {
            shortText.style.display = 'none';
            fullText.style.display = 'block';
            button.textContent = 'Menos...';
        } else {
            shortText.style.display = 'block';
            fullText.style.display = 'none';
            button.textContent = 'Mais...';
        }
    }
</script>
