<?php
session_start();
include __DIR__ . '/../../includes/criarNoticias.php';
include __DIR__ . '/../../includes/db.php';

// Eliminar notícia (POST) através de procedimento ou query
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['postID'])) {
    $postId = $_POST['postID'];

    try {
        // Exemplo de chamada de procedure (ajuste conforme a sua BD):
        $stmt = $pdo->prepare("CALL DELETE_POST(:postID)");
        $stmt->bindParam(':postID', $postId, PDO::PARAM_INT);
        $stmt->execute();

        echo "✅ Notícia eliminada com sucesso.";
    } catch (PDOException $e) {
        echo "❌ Erro: " . $e->getMessage();
    }
}

// Verifica permissões (apenas ADMIN ou COMPANY)
if (!isset($_SESSION['user']) || ($_SESSION['user']['user_type'] !== 'ADMIN' && $_SESSION['user']['user_type'] !== 'COMPANY')) {
    echo "<div class='alert alert-danger'>Acesso restrito.</div>";
    exit;
}

// Obter o ID do utilizador da sessão
$user_id = $_SESSION['user']['user_id'];

// Buscar o company_id correspondente ao utilizador
try {
    $stmt = $pdo->prepare("
        SELECT u.USER_ID, c.COMPANY_ID
        FROM USER u
        INNER JOIN COMPANY c ON c.USER_ID = u.USER_ID
        WHERE u.USER_ID = ?
    ");
    $stmt->execute([$user_id]);
    $company_id_result = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Erro ao buscar empresa: " . $e->getMessage();
    exit;
}

if (empty($company_id_result)) {
    echo '<p>Empresa inválida.</p>';
    exit;
}

$company_id = $company_id_result['COMPANY_ID'];

// Obter notícias para esta empresa
try {
    // Ajuste a query conforme a estrutura real da sua tabela
    $stmt2 = $pdo->prepare("
        SELECT p.*, c.COMPANY_NAME 
        FROM POST p 
        INNER JOIN COMPANY c ON c.COMPANY_ID = p.COMPANY_ID
        WHERE c.COMPANY_ID = ? 
          AND p.POST_STATUS = 'A'
        ORDER BY p.CREATED_AT DESC
    ");
    $stmt2->execute([$company_id]);
    $posts = $stmt2->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Erro ao obter notícias: " . $e->getMessage();
    exit;
}
?>

<div class="dash_list">
    <div class="dash_head">
        <h2 class="dash_title">Gestão de Notícias</h2>
        <button id="openModal" class="open-modal-btn" onclick="criarNoticia()">Nova Notícia</button>
    </div>

    <table class="dash_table">
        <tr class="dash_table_header">
            <th>Título</th>
            <th>Subtítulo</th>
            <th>Data de Criação</th>
            <th>Ações</th>
        </tr>
        <?php foreach ($posts as $post): ?>
            <tr class="dash_table_data">
                <td><?= htmlspecialchars($post['TITLE']) ?></td>
                <td><?= htmlspecialchars($post['SUBTITLE']) ?></td>
                <td><?= htmlspecialchars($post['CREATED_AT']) ?></td>
                <td>
                    <!-- Botão de editar -->
                    <input type="hidden" name="editarId" class="post-id" value="<?= htmlspecialchars($post['POST_ID']) ?>">
                    <button class="edit_button" onclick="editarNoticia(this)" type="button">Editar</button>

                    <!-- Formulário de eliminar -->
                    <form method="POST" class="deleteForm" style="display:inline;">
                        <input type="hidden" name="postID" value="<?= htmlspecialchars($post['POST_ID']) ?>">
                        <button class="delete_button" type="submit">Eliminar</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>