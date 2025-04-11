<?php
include __DIR__ . '/../../includes/db.php';
session_start();

if (!isset($_SESSION['user']) || $_SESSION['user']['user_type'] !== 'ADMIN') {
    echo "<div class='alert alert-danger'>Acesso restrito.</div>";
    exit;
}

if (isset($_GET['fetch'])) {
    header('Content-Type: application/json');

    // Totais gerais
    $totals = [
        'users' => $pdo->query("SELECT COUNT(*) FROM USER")->fetchColumn(),
        'empresas' => $pdo->query("SELECT COUNT(*) FROM COMPANY")->fetchColumn(),
        'produtos' => $pdo->query("SELECT COUNT(*) FROM PRODUCT")->fetchColumn(),
        'noticias' => $pdo->query("SELECT COUNT(*) FROM POST")->fetchColumn()
    ];

    // Ativos vs inativos
    $ativos = [
        'users' => [
            'ativos' => $pdo->query("SELECT COUNT(*) FROM USER WHERE USER_STATUS = 'A'")->fetchColumn(),
            'inativos' => $pdo->query("SELECT COUNT(*) FROM USER WHERE USER_STATUS = 'I'")->fetchColumn(),
        ],
        'empresas' => [
            'ativos' => $pdo->query("SELECT COUNT(*) FROM COMPANY WHERE COMPANY_STATUS = 'A'")->fetchColumn(),
            'inativos' => $pdo->query("SELECT COUNT(*) FROM COMPANY WHERE COMPANY_STATUS = 'I'")->fetchColumn(),
        ],
        'produtos' => [
            'ativos' => $pdo->query("SELECT COUNT(*) FROM PRODUCT WHERE PRODUCT_STATUS = 'A'")->fetchColumn(),
            'inativos' => $pdo->query("SELECT COUNT(*) FROM PRODUCT WHERE PRODUCT_STATUS = 'I'")->fetchColumn(),
        ],
    ];

    // Crescimento mensal (últimos 6 meses)
    function fetchPorMes($pdo, $tabela, $campoData) {
        $stmt = $pdo->prepare("
            SELECT DATE_FORMAT($campoData, '%Y-%m') AS mes, COUNT(*) AS total
            FROM $tabela
            WHERE $campoData >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
            GROUP BY mes
            ORDER BY mes ASC
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    $porMes = [
        'users' => fetchPorMes($pdo, 'USER', 'CREATED_AT'),
        'empresas' => fetchPorMes($pdo, 'COMPANY', 'CREATED_AT'),
        'produtos' => fetchPorMes($pdo, 'PRODUCT', 'CREATED_AT'),
    ];

    // Top empresas com mais produtos
    $topEmpresas = $pdo->query("
        SELECT c.COMPANY_NAME, COUNT(p.PRODUCT_ID) AS total
        FROM COMPANY c
        JOIN PRODUCT p ON c.COMPANY_ID = p.COMPANY_ID
        GROUP BY c.COMPANY_ID
        ORDER BY total DESC
        LIMIT 5
    ")->fetchAll(PDO::FETCH_ASSOC);

    // Top produtos com maior rank
    $topProdutos = $pdo->query("
        SELECT p.PRODUCT_NAME, p.PRODUCT_RANK, c.COMPANY_NAME
        FROM PRODUCT p
        JOIN COMPANY c ON c.COMPANY_ID = p.COMPANY_ID
        ORDER BY p.PRODUCT_RANK DESC
        LIMIT 5
    ")->fetchAll(PDO::FETCH_ASSOC);

    // Últimas notícias
    $ultimasNoticias = $pdo->query("
        SELECT p.POST_CONTENT, p.POST_STATUS, c.COMPANY_NAME
        FROM POST p
        JOIN COMPANY c ON c.COMPANY_ID = p.COMPANY_ID
        ORDER BY p.CREATED_AT DESC
        LIMIT 5
    ")->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'totals' => $totals,
        'ativos' => $ativos,
        'porMes' => $porMes,
        'topEmpresas' => $topEmpresas,
        'topProdutos' => $topProdutos,
        'ultimasNoticias' => $ultimasNoticias
    ]);
    exit;
}
?>

<link rel="stylesheet" href="assets/css/admin-cards.css">

<div class="dash_list">
    <div class="dash_head">
        <h2 class="dash_title">Estatísticas</h2>
        <button class="delete_button" onclick="loadPage('admin/dashboard')">Voltar</button>
    </div>

    <div id="stats-cards" class="stat-card-wrapper"></div>

    <div class="charts-grid">
        <canvas id="grafico-linhas"></canvas>
        <canvas id="doughnut-users"></canvas>
        <canvas id="doughnut-empresas"></canvas>
        <canvas id="doughnut-produtos"></canvas>
    </div>

    <div class="rankings">
        <div id="top-empresas"></div>
        <div id="top-produtos"></div>
        <div id="ultimas-noticias"></div>
    </div>
</div>

<!-- Chart.js e JS -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="assets/js/admin/estatisticas.js"></script>
