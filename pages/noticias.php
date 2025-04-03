<?php include '../includes/db.php'; ?>

<div class="wrapper">
    <div class="news-left">
        <div class="filtros">
            <?php include '../includes/filter_test.php'; ?>
        </div>
    </div>
    <div class="news-layout">
        <div class="news-container">
            <div class="news-grid">
                <?php
                try {
                    if (!empty($pdo)) {
                        $stmt = $pdo->query("SELECT p.POST_ID, p.POST_CONTENT, c.COMPANY_NAME, c.IMG_URL
                                        FROM POST p 
                                        INNER JOIN COMPANY c ON c.COMPANY_ID = p.COMPANY_ID
                                        WHERE p.POST_STATUS = 'A'");
                    }
                    while ($row = $stmt->fetch(mode: PDO::FETCH_ASSOC)) { ?>
                        <img src="<?php echo $row['IMG_URL'] ?>" alt="<?php $row['COMPANY_NAME'] ?>" class="company-img">
                        <div class="news-card">
                            <h3><?php echo htmlspecialchars($row['COMPANY_NAME']); ?></h3><br>
                            <p><?php echo htmlspecialchars($row['POST_CONTENT']); ?></p>
                            <button class="visit-button"
                                onclick="enviarResposta('<?php echo htmlspecialchars($row['POST_ID'], ENT_QUOTES, 'UTF-8'); ?>')">
                                Responda a Notícia
                            </button>
                            <div></div>
                            <textarea rows="5" cols="30" id="post_response<?php echo htmlspecialchars($row['POST_ID']); ?>"
                                class="post_response" placeholder="Escreva uma resposta..."></textarea>
                            <div class="news-grid">
                                <?php

                                try {
                                    $stmt2 = $pdo->prepare("SELECT pe.POST_EXT_CONTENT
                                                                FROM POST_EXT pe
                                                                INNER JOIN POST p ON p.POST_ID = pe.POST_ID
                                                                WHERE p.POST_STATUS = 'A'
                                                                AND p.POST_ID = ?");
                                    $stmt2->execute([$row['POST_ID']]); ?>
                                    <?php

                                    while ($row2 = $stmt2->fetch(PDO::FETCH_ASSOC)) {
                                        echo "<div class='post_messages' ><p>" . ($row2['POST_EXT_CONTENT']) . "</p></div>";
                                    }
                                } catch (PDOException $e) {
                                    echo "<small>Erro ao buscar respostas: " . $e->getMessage() . "</small>";
                                    $stmt = null;
                                } ?>
                            </div>
                        </div>
                    <?php }
                } catch (PDOException $e) {
                    echo "<p>Erro ao buscar empresas: " . $e->getMessage() . "</p>";
                    $stmt = null;
                }
                $stmt = null;
                ?>
            </div>
        </div>
    </div>
</div>
<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['post_id'], $_POST['resposta'])) {
    $postId = $_POST['post_id'];
    $resposta = $_POST['resposta'];

    try {
        if (!empty($pdo)) {
            $stmt = $pdo->prepare("CALL INSERT_POST_EXT(:post_id, :resposta)");
            $stmt->bindParam(':post_id', $postId);
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