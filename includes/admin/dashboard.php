<?php
session_start();
require_once '../db.php';

header('Content-Type: application/json');

// Acesso restrito
if (!isset($_SESSION['user']) || $_SESSION['user']['user_type'] !== 'ADMIN') {
    echo json_encode(['error' => 'Acesso não autorizado.']);
    exit;
}

try {
    // Contadores simples
    $total_users = $pdo->query("SELECT COUNT(*) FROM USER")->fetchColumn();
    $total_companies = $pdo->query("SELECT COUNT(*) FROM COMPANY")->fetchColumn();
    $total_products = $pdo->query("SELECT COUNT(*) FROM PRODUCT")->fetchColumn();

    // Dados para gráfico: Produtos por Categoria
    $stmt = $pdo->query("
        SELECT C.CATEGORY_NAME AS label, COUNT(P.PRODUCT_ID) AS total
        FROM CATEGORY C
        LEFT JOIN PRODUCT P ON C.CATEGORY_ID = P.CATEGORY_ID
        GROUP BY C.CATEGORY_ID
        ORDER BY total DESC
    ");
    
    $labels = [];
    $values = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $labels[] = $row['label'];
        $values[] = (int) $row['total'];
    }

    // Enviar JSON
    echo json_encode([
        'total_users' => $total_users,
        'total_companies' => $total_companies,
        'total_products' => $total_products,
        'chart_labels' => $labels,
        'chart_values' => $values
    ]);

} catch (PDOException $e) {
    echo json_encode(['error' => 'Erro na base de dados: ' . $e->getMessage()]);
    exit;
}
