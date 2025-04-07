<?php
include '../../includes/db.php';

$response = [
    'totalUsers' => $pdo->query("SELECT COUNT(*) FROM USERS")->fetchColumn(),
    'totalCompanies' => $pdo->query("SELECT COUNT(*) FROM COMPANY")->fetchColumn(),
    'totalProducts' => $pdo->query("SELECT COUNT(*) FROM PRODUCT")->fetchColumn(),
];

header('Content-Type: application/json');
echo json_encode($response);