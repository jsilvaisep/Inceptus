<?php
include __DIR__ . '/../../includes/criarProdutos.php';
include __DIR__ . '/../../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['prodID'])) {
    $productId = $_POST['prodID'];
    try {
        $stmt = $pdo->prepare("CALL DELETE_PRODUCT(:prodID)");
        $stmt->bindParam(':prodID', $productId, PDO::PARAM_STR); 
        $stmt->execute();
        
        echo "✅ Produto eliminado com sucesso.......";
    } catch (PDOException $e) {
        // Catch any errors and display them
        echo "❌ Erro: " . $e->getMessage();
    }
}

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
                <td>
                    <input type="hidden" name="editarId" class="product-id" value="<?= htmlspecialchars($product['PRODUCT_ID']) ?>">
                    <button class="edit_button" onclick="editarProduto(this)" type="button">Editar</button>
                </td>
                <td>
                    <form method="POST" class="deleteFormAdm">
                        <input type="hidden" name="prodID" class="product-id" value="<?= htmlspecialchars($product['PRODUCT_ID']) ?>">
                        <button class="delete_button" type="submit">Eliminar</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>