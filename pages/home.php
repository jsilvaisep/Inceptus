<?php include '../includes/db.php'; ?>

<div class="content-boxes">
    <div class="top-rated-section">
        <h2 class="top-rated-title">Top Rated Products</h2>
        <?php
        try {
            if (!empty($pdo)) {
                $stmt = $pdo->query("CALL PRODUCT_TOP");
            }
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $rating = (float) $row['PRODUCT_RANK'];
                $rounded = (int) round($rating);
                $fullStars = str_repeat('&#9733;', $rounded);
                $emptyStars = str_repeat('&#9734;', 5 - $rounded);
                echo "
                <div class='top-rated-card'>
                    <div class='card-content'>
                        <img src='{$row['IMG_URL']}' class='card-image'>
                        <div>
                            <strong>
                             <a href='?page=produtocompleto&id=" . urlencode($row['PRODUCT_ID']) . "' style='text-decoration: none; color: inherit;'>
                                  {$row['PRODUCT_NAME']}
                            </strong><br>
                            <small>{$row['PRODUCT_DESCRIPTION']}</small>
                        </div>
                    </div>
                    <div>
                        <span class='stars'>{$fullStars}{$emptyStars}</span>
                        </a>
                    </div>
                </div>";
            }
        } catch (PDOException $e) {
            echo "<p>Erro ao buscar produtos: " . $e->getMessage() . "</p>";
        }
        $stmt = null;
        ?>
    </div>
    <div class="top-rated-section">
        <h2 class="top-rated-title">Top Rated Services</h2>
        <?php
        try {
            if (!empty($pdo)) {
                $stmt = $pdo->query("CALL SERVICE_TOP");
            }
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $rating = (float) $row['PRODUCT_RANK'];
                $rounded = (int) round($rating);

                $fullStars = str_repeat('&#9733;', $rounded); 
                $emptyStars = str_repeat('&#9734;', 5 - $rounded);

                echo "
                <div class='top-rated-card'>
                    <div class='card-content'>
                        <img src='{$row['IMG_URL']}' class='card-image'>
                        <div>
                         <a href='?page=produtocompleto&id=" . urlencode($row['PRODUCT_ID']) . "' style='text-decoration: none; color: inherit;'>
                            {$row['PRODUCT_NAME']}
                            </strong><br>
                            <small>{$row['PRODUCT_DESCRIPTION']}</small>
                        </div>
                    </div>
                    <div>
                        <span class='stars'>{$fullStars}{$emptyStars}</span>
                        </a>
                    </div>
                </div>";
            }
        } catch (PDOException $e) {
            echo "<p>Erro ao buscar serviços: " . $e->getMessage() . "</p>";
        }
        $stmt = null;
        ?>
    </div>

    <div class="top-rated-section">
        <h2 class="top-rated-title">Top Rated companies</h2>
        <?php
        try {
            if (!empty($pdo)) {
                $stmt = $pdo->query("CALL COMPANY_TOP");
            }
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $rating = (float) $row['COMPANY_RANK'];
                $rounded = (int) round($rating);

                $fullStars = str_repeat('&#9733;', $rounded);
                $emptyStars = str_repeat('&#9734;', 5 - $rounded);

                echo "
                <div class='top-rated-card'>
                    <div class='card-content'>
                        <img src='{$row['IMG_URL']}' class='card-image'>
                        <div>
                        <a href='?page=empresacompleta&id=" . urlencode($row['COMPANY_ID']) . "' style='text-decoration: none; color: inherit;'>
                            {$row['COMPANY_NAME']}
                            </strong><br>
                            <small>{$row['COMPANY_DESCRIPTION']}</small>
                        </div>
                    </div>
                    <div>
                        <span class='stars'>{$fullStars}{$emptyStars}</span>
                        </a>
                    </div> 
                </div>";

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