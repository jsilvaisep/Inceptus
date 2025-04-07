<?php
include '../../includes/db.php';
session_start();

if (!isset($_SESSION['user']) || ($_SESSION['user']['user_type'] !== 'ADMIN' && $_SESSION['user']['user_type'] !== 'COMPANY')) {
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
    $stmt2 = $pdo->prepare("SELECT p.*, c.COMPANY_NAME 
                       FROM PRODUCT p 
                       INNER JOIN COMPANY c ON c.COMPANY_ID = p.COMPANY_ID
                       WHERE c.COMPANY_ID = ? AND p.PRODUCT_STATUS = 'A'");
    $stmt2->execute([$company_id]);
    $products = $stmt2->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "" . $e->getMessage();
}
?>

<div class="dash_list">
    <div class="dash_head">
        <h2 class="dash_title">Gestão de Produtos</h2>
        <button id="openModal" class="open-modal-btn" onclick="criarProduto()">Novo Produto</button>
    </div>
    <table class="dash_table">
        <tr class="dash_table_header">
            <th>Nome Produto</th>
            <th>Descrição Produto</th>
            <th>Rank</th>
        </tr>
        <?php foreach ($products as $product): ?>
            <tr class="dash_table_data">
                <td><?= htmlspecialchars($product['PRODUCT_NAME']) ?></td>
                <td><?= htmlspecialchars($product['PRODUCT_DESCRIPTION']) ?></td>
                <td><?= htmlspecialchars($product['PRODUCT_RANK']) ?></td>
                <td><button class="edit_button" value="<?= htmlspecialchars($product['PRODUCT_ID']) ?>">Editar</button></td>
                <td><button class="delete_button" value="<?= htmlspecialchars($product['PRODUCT_ID']) ?>">Eliminar</button>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>