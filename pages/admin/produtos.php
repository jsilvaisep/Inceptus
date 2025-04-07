<?php
include '../../includes/db.php';
session_start();

if (!isset($_SESSION['user']) || ($_SESSION['user']['user_type'] !== 'ADMIN') ){
    echo "<div class='alert alert-danger'>Acesso restrito.</div>";
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT c.COMPANY_NAME, p.* FROM PRODUCT p 
                            INNER JOIN COMPANY c ON c.COMPANY_ID = p.COMPANY_ID
                            ORDER BY c.COMPANY_NAME DESC ");
    $stmt->execute();
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo "erro" . $e->getMessage();
}

if (empty($products)) {
    echo '<p>Sem users registados.</p>';
    exit;
}

?>

<div class="dash_list">
    <div class="dash_head">
        <h2 class="dash_title">Gestão de Produtos</h2>
        <button class="delete_button" onclick="loadPage('admin/dashboard')">Voltar</button>
    </div>
    <table class="dash_table">
        <tr class="dash_table_header">
            <th>Company Name</th>
            <th>Product Name</th>
            <th>Product Description</th>
            <th>Product rank</th>
            <th></th>
            <th></th>
        </tr>
        <?php foreach ($products as $product): ?>
            <tr class="dash_table_data">
                <td><?= htmlspecialchars($product['COMPANY_NAME']) ?></td>
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