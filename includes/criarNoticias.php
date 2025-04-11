<?php
session_start();
include __DIR__ . '/db.php';
ob_clean();
header('Content-Type: application/json');

// Verifica se é um POST e se existe ao menos o título
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['title'])) {

    // Recupera o company_id a partir do utilizador logado
    $company_id = null;
    if (isset($_SESSION['user'])) {
        try {
            $user_id = $_SESSION['user']['user_id'];
            $stmt = $pdo->prepare("
                SELECT u.USER_ID, c.COMPANY_ID
                FROM USER u
                INNER JOIN COMPANY c ON c.USER_ID = u.USER_ID
                WHERE u.USER_ID = ?
            ");
            $stmt->execute([$user_id]);
            $company_id_result = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($company_id_result) {
                $company_id = $company_id_result['COMPANY_ID'];
            }
        } catch (PDOException $e) {
            echo json_encode(["error" => "Erro ao buscar identificação da empresa: " . $e->getMessage()]);
            exit;
        }
    }

    // Dados recebidos via POST
    $postTitle    = $_POST['title']              ?? null;
    $postSubtitle = $_POST['subtitle']           ?? null;
    $postContent  = $_POST['content']            ?? null;
    $postStatus   = $_POST['status']             ?? 'A'; // 'A' de ativo por padrão
    $postIdEditar = $_POST['post_id_editar']     ?? null;

    // Verifica se vai editar ou criar uma nova notícia
    if (!empty($postIdEditar)) {
        // Edita notícia existente
        try {
            $sql = "
                UPDATE POST
                SET 
                    TITLE        = :title,
                    SUBTITLE     = :subtitle,
                    POST_CONTENT = :pcontent,
                    POST_STATUS  = :pstatus,
                    UPDATED_AT   = NOW()
                WHERE POST_ID   = :pid
                  AND COMPANY_ID = :cid
            ";
            $stmt = $pdo->prepare($sql);

            $stmt->bindParam(':title',    $postTitle);
            $stmt->bindParam(':subtitle', $postSubtitle);
            $stmt->bindParam(':pcontent', $postContent);
            $stmt->bindParam(':pstatus',  $postStatus);
            $stmt->bindParam(':pid',      $postIdEditar);
            $stmt->bindParam(':cid',      $company_id);

            $stmt->execute();
            echo json_encode(["success" => "Notícia editada com sucesso!"]);
        } catch (PDOException $e) {
            echo json_encode(["error" => "Erro ao editar notícia: " . $e->getMessage()]);
        }
    } else {
        // Cria nova notícia
        try {
            // Se estiver usando procedure, ajuste conforme necessidade.
            // Exemplificando um INSERT direto (caso não tenha procedure).
            $sql = "
                INSERT INTO POST (COMPANY_ID, TITLE, SUBTITLE, POST_CONTENT, POST_STATUS, CREATED_AT)
                VALUES (:cid, :title, :subtitle, :pcontent, :pstatus, NOW())
            ";
            $stmt = $pdo->prepare($sql);

            $stmt->bindParam(':cid',      $company_id);
            $stmt->bindParam(':title',    $postTitle);
            $stmt->bindParam(':subtitle', $postSubtitle);
            $stmt->bindParam(':pcontent', $postContent);
            $stmt->bindParam(':pstatus',  $postStatus);

            $stmt->execute();
            echo json_encode(["success" => "Notícia criada com sucesso!"]);
        } catch (PDOException $e) {
            echo json_encode(["error" => "Erro ao criar notícia: " . $e->getMessage()]);
        }
    }
}