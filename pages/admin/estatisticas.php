<?php
include __DIR__ . '/../../includes/db.php';
session_start();

if (!isset($_SESSION['user']) || $_SESSION['user']['user_type'] !== 'ADMIN') {
    echo "<div class='alert alert-danger'>Acesso restrito.</div>";
    exit;
}

// Se for AJAX: devolver dados em JSON
if (isset($_GET['fetch'])) {
    header('Content-Type: application/json');

    // Totais
    $totalUsers = $pdo->query("SELECT COUNT(*) FROM USER")->fetchColumn();
    $totalEmpresas = $pdo->query("SELECT COUNT(*) FROM COMPANY")->fetchColumn();
    $totalProdutos = $pdo->query("SELECT COUNT(*) FROM PRODUCT")->fetchColumn();
    $totalNoticias = $pdo->query("SELECT COUNT(*) FROM POST")->fetchColumn();

    // Exemplo: utilizadores criados por mês nos últimos 6 meses
    $stmt = $pdo->query("
        SELECT DATE_FORMAT(CREATED_AT, '%Y-%m') AS mes, COUNT(*) AS total
        FROM USER
        WHERE CREATED_AT >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
        GROUP BY mes
        ORDER BY mes ASC
    ");
    $usersPorMes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'totals' => [
            'users' => $totalUsers,
            'empresas' => $totalEmpresas,
            'produtos' => $totalProdutos,
            'noticias' => $totalNoticias
        ],
        'grafico_usuarios' => $usersPorMes
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

    <div id="stats-cards" class="cards-grid"></div>

    <canvas id="grafico-usuarios" style="margin-top: 3rem; max-height: 400px;"></canvas>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="assets/js/admin/estatisticas.js"></script>
