<?php
include __DIR__ . '/../../includes/db.php';
session_start();

if (isset($_GET['fetch'])) {
    if (!isset($_SESSION['user']) || $_SESSION['user']['user_type'] !== 'ADMIN') {
        echo json_encode(['error' => 'Acesso restrito.']);
        exit;
    }

    header('Content-Type: application/json');
    $limit = 6;
    $page = isset($_GET['page']) ? max((int) $_GET['page'], 1) : 1;
    $offset = ($page - 1) * $limit;

    $stmt = $pdo->prepare("
        SELECT p.POST_ID, p.POST_CONTENT, p.POST_STATUS, c.COMPANY_NAME, p.POST_STATUS
        FROM POST p
        JOIN COMPANY c ON p.COMPANY_ID = c.COMPANY_ID
        ORDER BY p.POST_STATUS ASC, p.UPDATED_AT DESC 
        LIMIT :limit OFFSET :offset
    ");
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $news = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $total = $pdo->query("SELECT COUNT(*) FROM POST")->fetchColumn();

    echo json_encode([
        'news' => $news,
        'total' => $total,
        'page' => $page,
        'pages' => ceil($total / $limit)
    ]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'toggle') {
    if (!isset($_SESSION['user']) || $_SESSION['user']['user_type'] !== 'ADMIN') {
        echo json_encode(['success' => false, 'error' => 'Acesso restrito.']);
        exit;
    }

    $stmt = $pdo->prepare("SELECT POST_STATUS FROM POST WHERE POST_ID = ?");
    $stmt->execute([$_POST['POST_ID']]);
    $current = $stmt->fetchColumn();

    $newStatus = ($current === 'A') ? 'I' : 'A';

    $stmt = $pdo->prepare("UPDATE POST SET POST_STATUS = ? WHERE POST_ID = ?");
    $stmt->execute([$newStatus, $_POST['POST_ID']]);

    echo json_encode(['success' => true]);
    exit;
}

if (!isset($_SESSION['user']) || $_SESSION['user']['user_type'] !== 'ADMIN') {
    echo "<div class='alert alert-danger'>Acesso restrito.</div>";
    exit;
}
?>

<link rel="stylesheet" href="assets/css/admin-cards.css">

<div class="dash_list">
    <div class="dash_head">
        <h2 class="dash_title">Gestão de Notícias</h2>
        <button class="delete_button" onclick="loadPage('admin/empresadash')">Voltar</button>
    </div>

    <div id="news-table-container">A carregar notícias...</div>
    <div id="pagination-container" class="pagination"></div>
</div>

<script src="assets/js/admin/noticias.js"></script>