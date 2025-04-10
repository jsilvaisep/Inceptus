<?php
include '../../includes/db.php';
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);


if (!isset($_SESSION['user']) || ($_SESSION['user']['user_type'] !== 'ADMIN')) {
    echo "<div class='alert alert-danger'>Acesso restrito.</div>";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    $post_id = $_POST['post_id'] ?? ''; // Pegando o post_id enviado via POST

    if (empty($post_id)) {
        echo json_encode(['success' => false, 'message' => 'ID da notícia não fornecido.']);
        exit;
    }

    try {
        // Executando UPDATE no banco de dados para marcar o status da notícia como "inativa"
        $stmt = $pdo->prepare("UPDATE POST SET POST_STATUS = 'I' WHERE POST_ID = :post_id");
        $stmt->bindParam(':post_id', $post_id, PDO::PARAM_STR);  // Definindo o tipo do ID como string (não é INT)
        $stmt->execute();

        // Resposta em JSON
        echo json_encode([
            'success' => true,
            'message' => 'Notícia eliminada com sucesso!'
        ]);
    } catch (PDOException $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Erro ao eliminar notícia: ' . $e->getMessage()
        ]);
    }
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT p.POST_CONTENT, p.POST_ID, p.POST_STATUS, c.COMPANY_NAME FROM POST p 
                                INNER JOIN COMPANY c ON c.COMPANY_ID = p.COMPANY_ID
                                ORDER BY p.POST_STATUS ASC, p.UPDATED_AT DESC ");
    $stmt->execute();
    $news = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo "erro" . $e->getMessage();
}
// Debug para ver o que está sendo enviado de volta
header('Content-Type: application/json'); // Garante que o conteúdo retornado seja JSON

// Aqui deve vir o código que processa a requisição, e logo após ele retornar o JSON.
if (empty($news)) {
    echo json_encode(['success' => false, 'message' => 'Sem notícias registradas.']);
    exit;
}
if (empty($news)) {
    echo '<p>Sem noticias registados.</p>';
    exit;
}

?>

<div class="dash_list">
    <div class="dash_head">
        <h2 class="dash_title">Gestão de Empresas</h2>
        <button class="delete_button" onclick="loadPage('admin/dashboard')">Voltar</button>
    </div>
    <table class="dash_table">
        <tr class="dash_table_header">
            <th>Company Name</th>
            <th>Notícia</th>
            <th>Status</th>
            <th></th>
            <th></th>
        </tr>
        <?php foreach ($news as $post): ?>
            <tr class="dash_table_data">
                <td><?= htmlspecialchars($post['COMPANY_NAME']) ?></td>
                <td><?= htmlspecialchars($post['POST_CONTENT']) ?></td>
                <td><?= htmlspecialchars($post['POST_STATUS']) ?></td>
                <!--                <td>
                    <button class="edit_button"
                            onclick="submitEditarNoticiasAdmin('<?php /*= htmlspecialchars($post['POST_ID']) */ ?>')">Editar</button>
                </td>-->
                <td>
                    <button class="delete_button"
                            onclick="submitEliminarNoticiasAdmin('<?= htmlspecialchars($post['POST_ID']) ?>')">Eliminar
                    </button>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>