<?php include '../includes/db.php'; ?>

<div class="content-boxes" style="display: flex; gap: 30px; justify-content: space-between; padding: 20px; align-items: stretch;">

    <div class="left-content" style="flex: 1;">
        <h2 style="text-align: center; margin-bottom: 20px;">Top rated Products</h2>
        <?php
        try {
            $stmt = $pdo->query("CALL PRODUCT_TOP");
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo '
                <div style="display: flex; justify-content: space-between; align-items: center; padding: 15px 20px; background: white; border-radius: 15px; margin-bottom: 20px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); height: 110px;">
                    <div style="display: flex; align-items: center; gap: 15px;">
                        <img src="' . htmlspecialchars($row['IMG_URL']) . '" style="width: 50px; height: 50px; object-fit: cover; border-radius: 10px;">
                        <div>
                            <strong>' . htmlspecialchars($row['PRODUCT_NAME']) . '</strong><br>
                            <small>' . htmlspecialchars($row['PRODUCT_DESCRIPTION']) . '</small>
                        </div>
                    </div>
                    <div><span style="color: #be3144;">★★★★★</span></div>
                </div>';
            }
        } catch (PDOException $e) {
            echo "<p>Erro ao buscar produtos: " . $e->getMessage() . "</p>";
        }
        $stmt = null;
        ?>
    </div>
    <div class="center-content" style="flex: 1;">
        <h2 style="text-align: center; margin-bottom: 20px;">Trending Forums</h2>
        <?php
        try {
            $stmt = $pdo->query("CALL TRENDING_TOP");
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo '
                <div style="display: flex; justify-content: space-between; align-items: center; padding: 15px 20px; background: white; border-radius: 15px; margin-bottom: 20px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); height: 110px;">
                    <div>
                        <strong>' . htmlspecialchars($row['COMMENT_TEXT']) . '</strong><br>
                        <small style="color: #be3144;">💬 ' . htmlspecialchars($row['COMPANY_NAME']) . '</small>
                    </div>
                    <div><span style="color: #be3144;">★★★★★</span></div>
                </div>';
            }
        } catch (PDOException $e) {
            echo "<p>Erro ao buscar comentários: " . $e->getMessage() . "</p>";
        }
        $stmt = null;
        ?>
    </div>

    <div class="right-content" style="flex: 1;">
        <h2 style="text-align: center; margin-bottom: 20px;">Top rated companies</h2>
        <?php
        try {
            $stmt = $pdo->query("CALL COMPANY_TOP");
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo '
                <div style="display: flex; justify-content: space-between; align-items: center; padding: 15px 20px; background: white; border-radius: 15px; margin-bottom: 20px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); height: 110px;">
                    <div>
                        <strong>' . htmlspecialchars($row['COMPANY_NAME']) . '</strong><br>
                        <small>' . htmlspecialchars($row['COMPANY_DESCRIPTION']) . '</small>
                    </div>
                    <div><span style="color: #be3144;">★★★★★</span></div>
                </div>';
            }
        } catch (PDOException $e) {
            echo "<p>Erro ao buscar empresas: " . $e->getMessage() . "</p>";
        }
        $stmt = null;
        ?>
    </div>
</div>
<br>
<?php include '../includes/banner-news.php'; ?>
