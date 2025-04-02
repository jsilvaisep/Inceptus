<?php
include '../includes/db.php';
?>

<div class="news-layout">
    <div class="filtros">
        <?php include '../includes/filter_test.php'; ?>
    </div>
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
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) { ?>
                    <div class="news-card clickable-card">
                        <img src="<?php echo $row['IMG_URL'] ?>" alt="<?php $row['COMPANY_NAME'] ?>" class="company-img">
                        <div class="news-info"></div>
                        <h3><?php echo htmlspecialchars($row['COMPANY_NAME']); ?></h3><br>
                        <p><?php echo htmlspecialchars($row['POST_CONTENT']); ?></p>
                        <input type="text" id="post_response_<?php echo htmlspecialchars($row['POST_ID']); ?>"
                            class="post_response" placeholder="Escreva uma resposta...">
                    </div>
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
                            echo "<small>" . ($row2['POST_EXT_CONTENT']) . "</small><br>";
                        }
                    } catch (PDOException $e) {
                        echo "<small>Erro ao buscar respostas: " . $e->getMessage() . "</small>";
                        $stmt = null;
                    } ?>

                    <button onclick="enviarResposta('<?php echo htmlspecialchars($row['POST_ID']); ?>')"
                        class="visit-button">Responda a Notícia</button>
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
<!-- <script>
    function enviarResposta(postId) {
        const resposta = document.getElementById("post_response_" + postId).value;

        if (resposta.trim() !== "") {
            const xhr = new XMLHttpRequest();
            xhr.open("POST", "", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4) {
                    if (xhr.status === 200) {
                        //alert(resposta);
                        document.getElementById("post_response_" + postId).value = "";
                    } else {
                        alert("Erro ao enviar resposta.");
                    }
                }
            };

            xhr.send("post_id=" + encodeURIComponent(postId) + "&resposta=" + encodeURIComponent(resposta));
        } else {
            alert("Por favor, escreva uma resposta.");
        }
    }
</script>  -->