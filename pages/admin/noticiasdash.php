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
    $stmt2 = $pdo->prepare("SELECT p.*
                       FROM POST p 
                       WHERE p.COMPANY_ID = ? AND p.POST_STATUS = 'A'");
    $stmt2->execute([$company_id]);
    $posts = $stmt2->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "" . $e->getMessage();
}

if (empty($company_id_result)) {
    echo '<p>Sem notícias.</p>';
    exit;
}

?>

<div class="dash_list">
    <div class="dash_head">
        <h2 class="dash_title">Gestão de Empresas</h2>
        <button class="botao-voltar" onclick="loadPage('admin/empresadash')">Voltar</button>
        <button id="openModal" class="open-modal-btn" onclick="criarNoticia()">Nova Noticia</button>
    </div>
    <table class="dash_table">
        <tr class="dash_table_header">
            <th>Titulo</th>
            <th>Subtitulo</th>
            <th>Noticia</th>
            <th>Editar Noticia</th>
            <th>Eliminar Noticia</th>
        </tr>
        <?php foreach ($posts as $post): ?>
            <tr class="dash_table_data">
                <td><?= htmlspecialchars($post['TITLE']) ?></td>
                <td><?= htmlspecialchars($post['SUBTITLE']) ?></td>
                <td><?= htmlspecialchars($post['POST_CONTENT']) ?></td>
                <td>
                    <input type="hidden" name="editarId" class="post-id" value="<?= htmlspecialchars($post['POST_ID']) ?>">
                    <button class="edit_button" onclick="editarNoticia(this)" type="button">Editar</button>
                </td>
                <td>
                    <form method="POST" class="deleteForm">
                        <input type="hidden" name="postID" class="post-id" value="<?= htmlspecialchars($post['POST_ID']) ?>">
                        <button class="delete_button" type="submit">Eliminar</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>