<?php
include '../includes/db.php';
?>

<div class="company-layout">
    <div class="filtros">
        <?php include '../includes/filter.php'; ?>
    </div>


    <div class="company-container">
        <div class="search-section">
            <form class="search-box" data-page="empresas" onsubmit="return false;">
                <span class="search-icon">🔍</span>
                <input type="text" id="search-input" class="search-input" placeholder="Pesquisar empresas...">
                <div id="search-results" class="search-results-box"></div>
            </form>
            <div class="top-rated-section">
                <h2 class="top-rated-title">Feed de Notícias</h2>
                <?php
                try {
                    if (!empty($pdo)) {
                        $stmt = $pdo->query("SELECT p.POST_ID, p.POST_CONTENT, c.COMPANY_NAME, pe.POST_EXT_CONTENT 
                                        FROM POST p 
                                        INNER JOIN COMPANY c ON c.COMPANY_ID = p.COMPANY_ID
                                        LEFT JOIN POST_EXT pe ON pe.POST_ID = p.POST_ID");
                    }
                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) { ?>
                        <div class='top-rated-card'>
                            <div class='card-content'>
                                <div>
                                    <strong><?php echo htmlspecialchars($row['COMPANY_NAME']); ?></strong><br>
                                    <p><?php echo htmlspecialchars($row['POST_CONTENT']); ?></p>
                                    <input type="text" id="post_response_<?php echo htmlspecialchars($row['POST_ID']); ?>"
                                        class="post_response" placeholder="Escreva uma resposta...">
                                    <button
                                        onclick="enviarResposta('<?php echo htmlspecialchars($row['POST_ID']); ?>')">Responda
                                        a Notícia</button>
                                    <div>
                                        <small><?php echo ($row['POST_EXT_CONTENT']); ?></small>
                                    </div>
                                </div>
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
// Inserção da resposta no banco de dados
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