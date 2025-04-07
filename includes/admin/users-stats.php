<?php
include '../../includes/db.php';

$stmt = $pdo->query("
    SELECT DATE_FORMAT(CREATED_AT, '%Y-%m') AS month, COUNT(*) AS count
    FROM USERS
    GROUP BY month
    ORDER BY month
");

$labels = [];
$values = [];

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $labels[] = $row['month'];
    $values[] = $row['count'];
}

$response = [
    'labels' => $labels,
    'values' => $values,
];

header('Content-Type: application/json');
echo json_encode($response);