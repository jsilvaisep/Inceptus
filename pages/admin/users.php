<?php
include __DIR__ . '/../../includes/db.php';
session_start();

if (isset($_GET['fetch'])) {
    if (!isset($_SESSION['user']) || $_SESSION['user']['user_type'] !== 'ADMIN') {
        echo json_encode(['error' => 'Acesso restrito.']);
        exit;
    }

    header('Content-Type: application/json');
    $limit = 5;
    $page = isset($_GET['page']) ? max((int) $_GET['page'], 1) : 1;
    $offset = ($page - 1) * $limit;

    $stmt = $pdo->prepare("SELECT * FROM USER ORDER BY USER_STATUS ASC, UPDATED_AT DESC LIMIT :limit OFFSET :offset");
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $total = $pdo->query("SELECT COUNT(*) FROM USER")->fetchColumn();

    echo json_encode([
        'users' => $users,
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

    $stmt = $pdo->prepare("UPDATE USER SET USER_LOGIN = ?, USER_NAME = ?, USER_EMAIL = ? WHERE USER_ID = ?");
    $stmt->execute([$_POST['USER_LOGIN'], $_POST['USER_NAME'], $_POST['USER_EMAIL'], $_POST['USER_ID']]);
    echo json_encode(['success' => true]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'toggle') {
    if (!isset($_SESSION['user']) || $_SESSION['user']['user_type'] !== 'ADMIN') {
        echo json_encode(['success' => false, 'error' => 'Acesso restrito.']);
        exit;
    }

    $stmt = $pdo->prepare("SELECT USER_STATUS FROM USER WHERE USER_ID = ?");
    $stmt->execute([$_POST['USER_ID']]);
    $current = $stmt->fetchColumn();

    $newStatus = ($current === 'A') ? 'I' : 'A';

    $stmt = $pdo->prepare("UPDATE USER SET USER_STATUS = ? WHERE USER_ID = ?");
    $stmt->execute([$newStatus, $_POST['USER_ID']]);
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
        <h2 class="dash_title">Gestão de Utilizadores</h2>
        <button class="delete_button" onclick="loadPage('admin/dashboard')">Voltar</button>
    </div>

    <div id="user-table-container">A carregar utilizadores...</div>
    <div id="pagination-container" class="pagination"></div>
</div>

<div class="modal" id="editUserModal">
    <div class="modal-content">
        <h3>Editar Utilizador</h3>
        <form id="editUserForm">
            <input type="hidden" id="edit_user_id" name="USER_ID">
            <label for="edit_user_login">Login</label>
            <input type="text" id="edit_user_login" name="USER_LOGIN" required>
            <label for="edit_user_name">Nome</label>
            <input type="text" id="edit_user_name" name="USER_NAME" required>
            <label for="edit_user_email">Email</label>
            <input type="email" id="edit_user_email" name="USER_EMAIL" required>
            <div class="modal-buttons">
                <button type="submit" class="edit_button">Guardar</button>
                <button type="button" class="delete_button" onclick="closeEditModal()">Cancelar</button>
            </div>
        </form>
    </div>
</div>

<script src="assets/js/admin/users.js"></script>