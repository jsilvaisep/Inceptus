<?php include '../includes/db.php'; ?>

<div class="content-boxes" style="display: flex; gap: 30px; justify-content: space-between; padding: 20px; align-items: stretch;">
    <div class="left-content" style="flex: 1;">
        <h2 style="text-align: center; margin-bottom: 20px;">Top Rated Products</h2>
        <?php
        try {
            if (!empty($pdo)) {
                $stmt = $pdo->query("CALL PRODUCT_TOP");
            }
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $rating = (float) $row['PRODUCT_RANK'];
                $rounded = ($rating < 2.5) ? 2 : (int) round($rating);
                
                $fullStars = str_repeat('&#9733;', $rounded);
                $emptyStars = str_repeat('&#9734;', 5 - $rounded);
                
                echo "
                <div class='top-rated-card'>
                    <div style='display: flex; align-items: center; gap: 15px;'>
                        <img src='{$row['IMG_URL']}' class='card-image'>
                        <div>
                            <strong>{$row['PRODUCT_NAME']}</strong><br>
                            <small>{$row['PRODUCT_DESCRIPTION']}</small>
                        </div>
                    </div>
                    <div>
                        <span class='stars'>{$fullStars}{$emptyStars}</span>
                    </div>
                </div>";
            }
        } catch (PDOException $e) {
            echo "<p>Erro ao buscar produtos: " . $e->getMessage() . "</p>";
        }
        $stmt = null;
        ?>
    </div>
    <div class="center-content" style="flex: 1;">
        <h2 style="text-align: center; margin-bottom: 20px;">Top Rated Services</h2>
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
                    <div style='display: flex; align-items: center; gap: 15px;'>
                        <img src='{$row['IMG_URL']}' class='card-image'>
                        <div>
                            <strong>{$row['PRODUCT_NAME']}</strong><br>
                            <small>{$row['PRODUCT_DESCRIPTION']}</small>
                        </div>
                    </div>
                    <div>
                        <span class='stars'>{$fullStars}{$emptyStars}</span>
                    </div>
                </div>";
            }
        } catch (PDOException $e) {
            echo "<p>Erro ao buscar serviços: " . $e->getMessage() . "</p>";
        }
        $stmt = null;
        ?>
    </div>

    <div class="right-content" style="flex: 1;">
        <h2 style="text-align: center; margin-bottom: 20px;">Top Rated companies</h2>
        <?php
        try {
            if (!empty($pdo)) {
                $stmt = $pdo->query("CALL COMPANY_TOP");
            }
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $rating = (float) $row['COMPANY_RANK'];
                $rounded = ($rating < 2.5) ? 2 : (int) round($rating);

                $fullStars = str_repeat('&#9733;', $rounded);
                $emptyStars = str_repeat('&#9734;', 5 - $rounded);

                echo "
                <div class='top-rated-card'>
                    <div style='display: flex; align-items: center; gap: 15px;'>
                        <img src='{$row['IMG_URL']}' class='card-image'>
                        <div>
                            <strong>{$row['COMPANY_NAME']}</strong><br>
                            <small>{$row['COMPANY_DESCRIPTION']}</small>
                        </div>
                    </div>
                    <div>
                        <span class='stars'>{$fullStars}{$emptyStars}</span>
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

<style>
.top-rated-card {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px 20px;
    background: white;
    border-radius: 15px;
    margin-bottom: 20px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.05);
    height: 110px;
}

.top-rated-card .card-image {
    width: 50px;
    height: 50px;
    object-fit: contain;
    border-radius: 10px;
}

.stars {
    color: #be3144;
    font-size: 18px;
}
</style>