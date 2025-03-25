
<?php include '../includes/db.php'; ?>

<div class="home-container">
  <div class="search-section">
    <form method="GET" class="search-box">
      <span class="search-icon" onclick="this.closest('form').submit()">🔍</span>
      <input type="text" name="search" class="search-input" placeholder="Barra de pesquisa" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
    </form>
  </div>

  <div class="content-boxes">
    <!-- Coluna da Esquerda -->
    <div class="left-content">
      <div class="card-section">
        <h2>Top rated Products</h2>
        <?php
        try {
            $search = isset($_GET['search']) ? trim($_GET['search']) : '';
            $query = "SELECT PRODUCT_NAME, PRODUCT_DESCRIPTION, IMG_URL FROM PRODUCT";
            $params = [];

            if (!empty($search)) {
                $query .= " WHERE PRODUCT_NAME LIKE :search";
                $params['search'] = '%' . $search . '%';
            }

            $query .= " ORDER BY PRODUCT_RANK DESC LIMIT 5";

            $stmt = $pdo->prepare($query);
            $stmt->execute($params);

            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if (count($results) === 0) {
                echo '<p>Nenhum resultado encontrado.</p>';
            }

            foreach ($results as $row) {
                $img = htmlspecialchars($row['IMG_URL']) ?: 'assets/img/logo-symbol.png';
                echo '<div class="card">
                        <div class="card-left">
                            <img src="' . $img . '" alt="Produto">
                            <div>
                                <strong>' . htmlspecialchars($row['PRODUCT_NAME']) . '</strong><br>
                                <small>' . htmlspecialchars($row['PRODUCT_DESCRIPTION']) . '</small>
                            </div>
                        </div>
                        <div class="card-right">
                            💬 <span class="stars">★★★★★</span>
                        </div>
                    </div>';
            }
        } catch (PDOException $e) {
            echo "<p>Erro ao buscar produtos: " . $e->getMessage() . "</p>";
        }
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
            $stmt = $pdo->query("SELECT COMPANY_NAME, COMPANY_DESCRIPTION FROM COMPANY ORDER BY COMPANY_RANK DESC LIMIT 3");
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
        ?>
      </div>
    <br><br>
      <div class="card-section">
        <h2>Trending Forums</h2>
        <?php
        try {
            $stmt = $pdo->query("SELECT c.COMMENT_TEXT, com.COMPANY_NAME 
                                 FROM COMMENT c
                                 JOIN COMPANY com ON c.COMPANY_ID = com.COMPANY_ID
                                 ORDER BY c.CREATED_AT DESC LIMIT 3");
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo '<div class="card small-card">
                        <div><strong>' . htmlspecialchars($row['COMMENT_TEXT']) . '</strong></div>
                        <div style="color: #be3144;">💬 ' . htmlspecialchars($row['COMPANY_NAME']) . '</div>
                      </div>';
            }
        } catch (PDOException $e) {
            echo "<p>Erro ao buscar comentários: " . $e->getMessage() . "</p>";
        }
        ?>
      </div>
    </div>
  </div>
</div>
