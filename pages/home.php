
<?php include '../includes/db.php'; ?>

<br><br><br><br><br> 
  <div class="content-boxes">
    <!-- Coluna da Esquerda -->
    <div class="left-content">
      <div class="card-section">
        <h2>Top rated Products</h2>
        <?php
        try {
            $stmt = $pdo->query("CALL PRODUCT_TOP");
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo '<div class="card">
                        <div class="card-left">
                          <img src ="' . htmlspecialchars($row['IMG_URL']) .'" />
                          <div>
                              <strong>' . htmlspecialchars($row['PRODUCT_NAME']) . '</strong><br>
                              <small>' . htmlspecialchars($row['PRODUCT_DESCRIPTION']) . '</small>
                          </div>
                        </div>  
                        
                        <div class="card-right">
                          <span class="stars">★★★★★</scan>
                        </div>
                    </div>';
            }
        } catch (PDOException $e) {
            echo "<p>Erro ao buscar produtos: " . $e->getMessage() . "</p>";
        }
        $stmt->closeCursor();
        ?>
      </div>
      <div class="banner-container" style="margin-top: 30px; border-radius: 20px; overflow: hidden; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);">
        <img src="assets/img/inceptus-banner.png" alt="Banner Inceptus" style="width: 100%; height: auto; display: block; border-radius: 20px; object-fit: cover;">
      </div>
    </div>
    <!-- Coluna da Direita -->
    <div class="right-content">
      <div class="card-section">
        <h2>Top rated companys</h2>
        <?php
        try {
            $stmt = $pdo->query("CALL COMPANY_TOP");
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo '<div class="card small-card">
                        <div>
                            <strong>' . htmlspecialchars($row['COMPANY_NAME']) . '</strong><br>
                            <small>' . htmlspecialchars($row['COMPANY_DESCRIPTION']) . '</small>
                        </div>
                        <div class="stars">★★★★★</div>
                    </div>';
            }
        } catch (PDOException $e) {
            echo "<p>Erro ao buscar empresas: " . $e->getMessage() . "</p>";
        }
        $stmt->closeCursor();
        ?>
      </div>
    <br><br>
      <div class="card-section">
        <h2>Trending Forums</h2>
        <?php
        try {
            $stmt = $pdo->query("CALL TRENDING_TOP");
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo '<div class="card small-card">
                        <div><strong>' . htmlspecialchars($row['COMMENT_TEXT']) . '</strong></div>
                        <div style="color: #be3144;">💬 ' . htmlspecialchars($row['COMPANY_NAME']) . '</div>
                      </div>';
            }
        } catch (PDOException $e) {
            echo "<p>Erro ao buscar comentários: " . $e->getMessage() . "</p>";
        }
        $stmt->closeCursor();
        ?>
      </div>
    </div>
  </div>
</div>
