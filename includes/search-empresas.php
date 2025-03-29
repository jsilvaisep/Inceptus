<?php
require_once 'db.php';

if (!isset($_GET['q'])) {
    echo '';
    exit;
}

$search = trim($_GET['q']);
$term = "%$search%";

$stmt = $pdo->prepare("SELECT COMPANY_ID, COMPANY_NAME, COMPANY_DESCRIPTION, IMG_URL FROM COMPANY WHERE COMPANY_NAME LIKE ? LIMIT 10");
$stmt->execute([$term]);
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (count($results) === 0) {
    echo '<p class="no-results">Nenhuma empresa encontrado.</p>';
    exit;
}

foreach ($results as $company) {
    echo '<div class="search-result-item clickable-company" data-id="' . $company['COMPANY_ID'] . '">';
    echo '<img class="company-img" src="' . htmlspecialchars($company['IMG_URL']) . '" alt="Imagem da Empresa">';
    echo '<div class="info">';
    echo '<strong>' . htmlspecialchars($company['COMPANY_NAME']) . '</strong>';
    echo '<p>' . htmlspecialchars($company['COMPANY_DESCRIPTION']) . '</p>';
    echo '</div></div>';
}
?>
