<?php
include '../../includes/db.php';
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);


if (!isset($_SESSION['user']) || ($_SESSION['user']['user_type'] !== 'COMPANY')) {
    echo "<div class='alert alert-danger'>Acesso restrito.</div>";
    exit;
}

$user_id = $_SESSION['user']['user_id'];

try {
    $stmt = $pdo->prepare("SELECT u.USER_ID, c.COMPANY_ID
                       FROM USER u 
                       INNER JOIN COMPANY c ON c.USER_ID = u.USER_ID
                       WHERE u.USER_ID = ? ");
    $stmt->execute([$user_id]);
    $company_id_result = $stmt->fetch(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo "" . $e->getMessage();
}

if (empty($company_id_result)) {
    echo '<p>Empresa inválida.</p>';
    exit;
}

$company_id = $company_id_result['COMPANY_ID'];

try {
    $stmt2 = $pdo->prepare("SELECT c.*, cp.COMPANY_NAME, p.PRODUCT_NAME
                       FROM COMMENT c 
                       INNER JOIN COMPANY cp ON cp.COMPANY_ID = c.COMPANY_ID
                       INNER JOIN PRODUCT p ON p.PRODUCT_ID = c.PRODUCT_ID
                       WHERE cp.COMPANY_ID = ? AND c.COMMENT_STATUS = 'A'");
    $stmt2->execute([$company_id]);
    $comments = $stmt2->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "" . $e->getMessage();
}

if (empty($company_id_result)) {
    echo '<p>Sem comentários.</p>';
    exit;
}

?>

<div class="dash_list">
    <div class="dash_head">
        <h2 class="dash_title">Gestão de Empresas</h2>
        <button class="delete_button" onclick="loadPage('admin/dashboard')">Voltar</button>
    </div>
    <table class="dash_table">
        <tr class="dash_table_header">
            <th>Product Name</th>
            <th>Comment</th>
            <th>Status</th>
            <th></th>
            <th></th>
        </tr>
        <?php foreach ($comments as $comment): ?>
            <tr class="dash_table_data">
                <td><?= htmlspecialchars($comment['PRODUCT_NAME']) ?></td>
                <td><?= htmlspecialchars($comment['COMMENT_TEXT']) ?></td>
                <td><?= htmlspecialchars($comment['COMMENT_STATUS']) ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>