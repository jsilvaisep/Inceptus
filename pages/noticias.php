<?php include_once '../includes/db.php';
session_start();
if (isset($_SESSION['user'])) {
    $userID = $_SESSION['user']['user_id'];
} else {
    header("Location: /pages/redirect.php");
    exit;
}
?>
    <div class="wrapper">
        <div class="news-container">
            <div class="news-sidebar">
                <?php include_once '../includes/filter.php'; ?>
            </div>
            <div class="news-page">
                <h1>Notícias</h1>
            <div class="news-grid">
                <?php
                try {
                    if (!empty($pdo)) {
                        // Definir a quantidade de itens por página
                        $itens_por_pagina = 10;

                        // Página atual
                        $pagina = isset($_GET['pagina']) ? intval($_GET['pagina']) : 1;
                        $offset = ($pagina - 1) * $itens_por_pagina;

                        // Contar o total de posts para calcular páginas
                        $total_posts = $pdo->query("SELECT COUNT(*) FROM POST WHERE POST_STATUS = 'A'")->fetchColumn();
                        $total_paginas = ceil($total_posts / $itens_por_pagina);

                        // Buscar posts com paginação
                        $stmt = $pdo->prepare("SELECT p.POST_ID, p.POST_CONTENT, p.CREATED_AT,
                           c.COMPANY_NAME, c.IMG_URL, u.USER_NAME
                            FROM POST p
                            INNER JOIN COMPANY c ON c.COMPANY_ID = p.COMPANY_ID
                            INNER JOIN USER u ON u.USER_ID = c.USER_ID
                            WHERE p.POST_STATUS = 'A'
                            ORDER BY p.CREATED_AT DESC
                            LIMIT :limit OFFSET :offset");
                        $stmt->bindParam(':limit', $itens_por_pagina, PDO::PARAM_INT);
                        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
                        $stmt->execute();
                    }
                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        // Truncar o conteúdo a 100 caracteres
                        $truncatedContent = strlen($row['POST_CONTENT']) > 100 ?
                            substr($row['POST_CONTENT'], 0, 100) . '...' :
                            $row['POST_CONTENT'];

                        // Formatação da data
                        $date = new DateTime($row['CREATED_AT']);
                        $formattedDate = $date->format('d/m/Y H:i');
                        ?>
                        <div class="news-card" data-id="<?php echo htmlspecialchars($row['POST_ID']); ?>">
                            <div class="news-card-header">
                                <img src="<?php echo htmlspecialchars($row['IMG_URL']); ?>" alt="<?php echo htmlspecialchars($row['COMPANY_NAME']); ?>" class="company-logo">
                                <h3><?php echo htmlspecialchars($row['COMPANY_NAME']); ?></h3>
                            </div>
                            <div class="news-card-content">
                                <p class="news-text"><?php echo htmlspecialchars($truncatedContent); ?></p>
                            </div>
                            <div class="news-card-footer">
                                <span class="news-author">Por: <?php echo htmlspecialchars($row['USER_NAME']); ?></span>
                                <span class="news-date"><?php echo $formattedDate; ?></span>
                            </div>
                            <button class="open-modal-btn" data-id="<?php echo htmlspecialchars($row['POST_ID']); ?>">
                                Ler mais
                            </button>
                        </div>
                    <?php }
                } catch (PDOException $e) {
                    echo "<p class='error'>Erro ao buscar notícias: " . $e->getMessage() . "</p>";
                    $stmt = null;
                }
                $stmt = null;
                ?>
            </div>
        </div>
    </div>
<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['post_id'], $_POST['resposta'])) {
    $postId = $_POST['post_id'];
    $resposta = $_POST['resposta'];

    try {
        if (!empty($pdo)) {
            $stmt = $pdo->prepare("CALL INSERT_POST_EXT(:post_id, :user_id, :resposta)");
            $stmt->bindParam(':post_id', $postId);
            $stmt->bindParam(':user_id', $userID);
            $stmt->bindParam(':resposta', $resposta);
            $stmt->execute();
            echo "Resposta enviada com sucesso!";
            $stmt = null;
            exit;
        } else {
            echo "Erro na conexão com o banco de dados.";
            $stmt = null;
            exit;
        }
    } catch (PDOException $e) {
        echo "Erro ao enviar resposta: " . $e->getMessage();
        $stmt = null;
        exit;
    }
}
?>