<?php
include '../includes/db.php';
?>

<div class="top-rated-section">
    <?php
    try {
        if (!empty($pdo)) {
            $stmt = $pdo->query(
                        "SELECT p.POST_CONTENT, c.COMPANY_NAME, pe.POST_EXT_CONTENT, pe.POST_ID FROM POST p
                                INNER JOIN COMPANY c ON c.COMPANY_ID = p.COMPANY_ID
                                LEFT JOIN POST_EXT pe ON p.POST_ID = pe.POST_ID
                                WHERE c.COMPANY_STATUS = 'A'"
            );
        }
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "
                <div class='top-rated-card'>
                    <div class='card-content'>
                        <div>
                            <strong>{$row['COMPANY_NAME']}</strong><br>
                            <small>{$row['POST_CONTENT']}</small>";?>
                                <?php
                                    if (!empty($row['POST_ID'])){
                                        echo "<button>Respostas</button>";
                                    }

                                ?>
                                <?php
                echo "
                                        </div>
                        <div>
                            <small>{$row['POST_EXT_CONTENT']}</small>
                        </div>
                            </div>
                        <div>    
                    </div>
                </div>";
        }
        $stmt = null;
    } catch (PDOException $e) {
        echo "<p>Erro ao pesuisar empresas: " . $e->getMessage() . "</p>";
        $stmt = null;
    }
    $stmt = null;
    ?>
</div>

