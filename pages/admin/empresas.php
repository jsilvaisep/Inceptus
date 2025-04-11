<?php
include __DIR__ . '/../../includes/db.php';
session_start();

// AJAX: Listar empresas com paginação
if (isset($_GET['fetch'])) {
    if (!isset($_SESSION['user']) || $_SESSION['user']['user_type'] !== 'ADMIN') {
        echo json_encode(['error' => 'Acesso restrito.']);
        exit;
    }

    header('Content-Type: application/json');
    $limit = 6;
    $page = isset($_GET['page']) ? max((int)$_GET['page'], 1) : 1;
    $offset = ($page - 1) * $limit;

    $stmt = $pdo->prepare("SELECT * FROM COMPANY ORDER BY CREATED_AT DESC LIMIT :limit OFFSET :offset");
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $companies = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $total = $pdo->query("SELECT COUNT(*) FROM COMPANY")->fetchColumn();

    echo json_encode([
        'companies' => $companies,
        'total' => $total,
        'page' => $page,
        'pages' => ceil($total / $limit)
    ]);
    exit;
}

// AJAX: Atualizar dados da empresa
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'update') {
    if (!isset($_SESSION['user']) || $_SESSION['user']['user_type'] !== 'ADMIN') {
        echo json_encode(['success' => false, 'error' => 'Acesso restrito.']);
        exit;
    }

    $stmt = $pdo->prepare("UPDATE COMPANY SET COMPANY_NAME = ?, COMPANY_EMAIL = ?, COMPANY_SITE = ? WHERE COMPANY_ID = ?");
    $stmt->execute([
        $_POST['COMPANY_NAME'],
        $_POST['COMPANY_EMAIL'],
        $_POST['COMPANY_SITE'],
        $_POST['COMPANY_ID']
    ]);

    echo json_encode(['success' => true]);
    exit;
}

// AJAX: Eliminar empresa
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'delete') {
    if (!isset($_SESSION['user']) || $_SESSION['user']['user_type'] !== 'ADMIN') {
        echo json_encode(['success' => false, 'error' => 'Acesso restrito.']);
        exit;
    }

    $stmt = $pdo->prepare("DELETE FROM COMPANY WHERE COMPANY_ID = ?");
    $stmt->execute([$_POST['COMPANY_ID']]);

    echo json_encode(['success' => true]);
    exit;
}

// HTML da página
if (!isset($_SESSION['user']) || $_SESSION['user']['user_type'] !== 'ADMIN') {
    echo "<div class='alert alert-danger'>Acesso restrito.</div>";
    exit;
}
?>

<link rel="stylesheet" href="assets/css/admin-empresas.css">

<div class="dash_list">
    <div class="dash_head">
        <h2 class="dash_title">Gestão de Empresas</h2>
        <button class="delete_button" onclick="loadPage('admin/dashboard')">Voltar</button>
    </div>

    <div id="company-table-container">A carregar empresas...</div>
    <div id="pagination-container" class="pagination"></div>
</div>

<div class="modal" id="editCompanyModal">
    <div class="modal-content">
        <h3>Editar Empresa</h3>
        <form id="editCompanyForm">
            <input type="hidden" id="edit_company_id" name="COMPANY_ID">
            <label for="edit_company_name">Nome</label>
            <input type="text" id="edit_company_name" name="COMPANY_NAME" required>
            <label for="edit_company_email">Email</label>
            <input type="email" id="edit_company_email" name="COMPANY_EMAIL" required>
            <label for="edit_company_site">Site</label>
            <input type="url" id="edit_company_site" name="COMPANY_SITE" required>
            <div class="modal-buttons">
                <button type="submit" class="edit_button">Guardar</button>
                <button type="button" class="delete_button" onclick="closeEditModal()">Cancelar</button>
            </div>
        </form>
    </div>
</div>

<script src="assets/js/admin/empresas.js"></script>
