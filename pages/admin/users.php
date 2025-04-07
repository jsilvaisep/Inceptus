<?php
include '../../includes/db.php';
session_start();

if (!isset($_SESSION['user']) || ($_SESSION['user']['user_type'] !== 'ADMIN') ){
    echo "<div class='alert alert-danger'>Acesso restrito.</div>";
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT * FROM USER u ORDER BY u.USER_LOGIN DESC ");
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo "erro" . $e->getMessage();
}

if (empty($users)) {
    echo '<p>Sem users registados.</p>';
    exit;
}

?>

<div class="dash_list">
    <div class="dash_head">
        <h2 class="dash_title">Gestão de Utilizadores</h2>
        <button class="delete_button" onclick="loadPage('admin/dashboard')">Voltar</button>
    </div>
    <table class="dash_table">
        <tr class="dash_table_header">
            <th>User Login</th>
            <th>User Name</th>
            <th>User email</th>
            <th></th>
            <th></th>
        </tr>
        <?php foreach ($users as $user): ?>
            <tr class="dash_table_data">
                <td><?= htmlspecialchars($user['USER_LOGIN']) ?></td>
                <td><?= htmlspecialchars($user['USER_NAME']) ?></td>
                <td><?= htmlspecialchars($user['USER_EMAIL']) ?></td>
                <td><button class="edit_button" value="<?= htmlspecialchars($user['USER_ID']) ?>">Editar</button></td>
                <td><button class="delete_button" value="<?= htmlspecialchars($user['USER_ID']) ?>">Eliminar</button>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>