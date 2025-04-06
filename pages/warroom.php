<?php
include '../includes/db.php';

// Buscar categorias e produtos
$stmtCategories = $pdo->query("SELECT CATEGORY_ID, CATEGORY_NAME FROM CATEGORY WHERE CATEGORY_TYPE = 'PRODUTO'");
$categories = $stmtCategories->fetchAll(PDO::FETCH_ASSOC);

$stmtProducts = $pdo->query("SELECT p.*, c.COMPANY_NAME, p.CATEGORY_ID 
                             FROM PRODUCT p
                             INNER JOIN COMPANY c ON p.COMPANY_ID = c.COMPANY_ID
                             WHERE p.PRODUCT_STATUS = 'A'");
$products = $stmtProducts->fetchAll(PDO::FETCH_ASSOC);

// Organizar produtos por ID e categorias para JS
$productMap = [];
foreach ($products as $p) {
    $productMap[$p['CATEGORY_ID']][] = $p;
}
?>

<!-- Conteúdo da página Warroom -->
<div>
    <h1 class="warroom-title">WARROOM</h1>

    <!-- Filtro de categorias -->
    <?php if (empty($categories)): ?>
        <p style="text-align: center; color: #be3144;">Nenhuma categoria encontrada na base de dados.</p>
    <?php else: ?>
        <div class="category-filter-container">
            <label for="category-filter">Escolha uma categoria:</label>
            <select id="category-filter">
                <option value="all">Todas as Categorias</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?= $category['CATEGORY_ID'] ?>"><?= htmlspecialchars($category['CATEGORY_NAME']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    <?php endif; ?>

    <!-- Seletores de produtos -->
    <?php if (empty($products)): ?>
        <p style="text-align: center; color: #be3144;">Nenhum produto ativo encontrado na base de dados.</p>
    <?php else: ?>
        <div class="selectors-container">
            <?php for ($i = 1; $i <= 3; $i++): ?>
                <div class="product-select" data-slot="<?= $i ?>">
                    <!-- Produtos são carregados dinamicamente pelo JavaScript -->
                </div>
            <?php endfor; ?>
        </div>

        <!-- Tabela de comparação -->
        <table class="comparison-table" id="comparison-table" style="display: none;">
            <thead>
            <tr>
                <!-- Cabeçalho gerado dinamicamente pelo JS -->
            </tr>
            </thead>
            <tbody id="comparison-body">
            <!-- Corpo da tabela preenchido dinamicamente pelo JS -->
            </tbody>
        </table>


        <!-- Dados para o JavaScript -->
        <div id="warroom-data" data-products='<?= json_encode($productMap, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>'></div>
    <?php endif; ?>
</div>