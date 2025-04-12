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
    $stmt2 = $pdo->prepare("SELECT t.TAG_NAME, c.* FROM COMPANY c 
                            INNER JOIN TAG_COMPANY tc ON tc.COMPANY_ID = c.COMPANY_ID
                            INNER JOIN TAG t ON t.TAG_ID = tc.TAG_ID
                            WHERE c.COMPANY_ID = ? AND t.TAG_STATUS = 'A'");
    $stmt2->execute([$company_id]);
    $comments = $stmt2->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "" . $e->getMessage();
}

if (empty($company_id_result)) {
    echo '<p>Sem comentários.</p>';
    exit;
}

// CALL INSERT_TAG (:tag_name,:company_id)

?>

<div class="dash_list">
    <div class="dash_head">
        <button class="delete_button" onclick="loadPage('admin/empresadash')">Voltar</button>
        <h2 class="dash_title">Gestão de Empresas</h2>
        <button class="delete_button" onclick="">Criar Tag</button>
    </div>
    <table class="dash_table">
        <tr class="dash_table_header">
            <th>Tag Name</th>
            <th></th>
            <th></th>
        </tr>
        <?php foreach ($comments as $comment): ?>
            <tr class="dash_table_data">
                <td><?= htmlspecialchars($comment['TAG_NAME']) ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>