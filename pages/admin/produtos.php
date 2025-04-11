<?php
include __DIR__ . '/../../includes/db.php';
session_start();

if (isset($_GET['fetch'])) {
    if (!isset($_SESSION['user']) || $_SESSION['user']['user_type'] !== 'ADMIN') {
        echo json_encode(['error' => 'Acesso restrito.']);
        exit;
    }

    header('Content-Type: application/json');
    $limit = 6;
    $page = isset($_GET['page']) ? max((int)$_GET['page'], 1) : 1;
    $offset = ($page - 1) * $limit;

    $stmt = $pdo->prepare("
        SELECT p.*, c.COMPANY_NAME 
        FROM PRODUCT p 
        JOIN COMPANY c ON p.COMPANY_ID = c.COMPANY_ID
        ORDER BY p.CREATED_AT DESC 
        LIMIT :limit OFFSET :offset
    ");
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $total = $pdo->query("SELECT COUNT(*) FROM PRODUCT")->fetchColumn();

    echo json_encode([
        'products' => $products,
        'total' => $total,
        'page' => $page,
        'pages' => ceil($total / $limit)
    ]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'update') {
    if (!isset($_SESSION['user']) || $_SESSION['user']['user_type'] !== 'ADMIN') {
        echo json_encode(['success' => false, 'error' => 'Acesso restrito.']);
        exit;
    }

    $stmt = $pdo->prepare("UPDATE PRODUCT SET PRODUCT_NAME = ?, PRODUCT_DESCRIPTION = ? WHERE PRODUCT_ID = ?");
    $stmt->execute([
        $_POST['PRODUCT_NAME'],
        $_POST['PRODUCT_DESCRIPTION'],
        $_POST['PRODUCT_ID']
    ]);

    echo json_encode(['success' => true]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'toggle') {
    if (!isset($_SESSION['user']) || $_SESSION['user']['user_type'] !== 'ADMIN') {
        echo json_encode(['success' => false, 'error' => 'Acesso restrito.']);
        exit;
    }

    $stmt = $pdo->prepare("SELECT PRODUCT_STATUS FROM PRODUCT WHERE PRODUCT_ID = ?");
    $stmt->execute([$_POST['PRODUCT_ID']]);
    $current = $stmt->fetchColumn();

    $newStatus = ($current === 'A') ? 'I' : 'A';

    $stmt = $pdo->prepare("UPDATE PRODUCT SET PRODUCT_STATUS = ? WHERE PRODUCT_ID = ?");
    $stmt->execute([$newStatus, $_POST['PRODUCT_ID']]);

    echo json_encode(['success' => true]);
    exit;
}

if (!isset($_SESSION['user']) || $_SESSION['user']['user_type'] !== 'ADMIN') {
    echo "<div class='alert alert-danger'>Acesso restrito.</div>";
    exit;
}
?>

<link rel="stylesheet" href="assets/css/admin-cards.css">

<div class="dash_list">
    <div class="dash_head">
        <h2 class="dash_title">Gestão de Produtos</h2>
        <button class="delete_button" onclick="loadPage('admin/dashboard')">Voltar</button>
    </div>

    <div id="product-table-container">A carregar produtos...</div>
    <div id="pagination-container" class="pagination"></div>
</div>

<div class="modal" id="editProductModal">
    <div class="modal-content">
        <h3>Editar Produto</h3>
        <form id="editProductForm">
            <input type="hidden" id="edit_product_id" name="PRODUCT_ID">
            <label for="edit_product_name">Nome</label>
            <input type="text" id="edit_product_name" name="PRODUCT_NAME" required>
            <label for="edit_product_description">Descrição</label>
            <input type="text" id="edit_product_description" name="PRODUCT_DESCRIPTION" required>
            <div class="modal-buttons">
                <button type="submit" class="edit_button">Guardar</button>
                <button type="button" class="delete_button" onclick="closeEditModal()">Cancelar</button>
            </div>
        </form>
    </div>
</div>

<script src="assets/js/admin/produtos.js"></script>
