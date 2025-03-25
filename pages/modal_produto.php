<?php
include '../includes/db.php';

if (!isset($_GET['id'])) {
    echo '<p>Produto não encontrado.</p>';
    exit;
}

$id = intval($_GET['id']);

try {
    $stmt = $pdo->prepare("SELECT p.PRODUCT_NAME, p.PRODUCT_DESCRIPTION, p.IMG_URL, c.COMPANY_NAME
                           FROM PRODUCT p
                           JOIN COMPANY c ON p.COMPANY_ID = c.COMPANY_ID
                           WHERE p.PRODUCT_ID = :id");
    $stmt->execute(['id' => $id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$product) {
        echo '<p>Produto não encontrado.</p>';
        exit;
    }

    echo '<div class="modal-container">
            <div class="modal-image">
                <img src="' . htmlspecialchars($product['IMG_URL']) . '" alt="Imagem do produto">
            </div>
            <div class="modal-details">
                <h2>Nome do produto: ' . htmlspecialchars($product['PRODUCT_NAME']) . '</h2>
                <h4>' . htmlspecialchars($product['COMPANY_NAME']) . '</h4>
                <p><strong>Descrição do produto:</strong><br>' . htmlspecialchars($product['PRODUCT_DESCRIPTION']) . '</p>
                <h3>Últimos comentários:</h3>
                <div class="reviews">';

    $comments = $pdo->prepare("SELECT u.USER_NAME, u.IMG_URL as USER_IMG, c.COMMENT_TEXT, c.CREATED_AT
                               FROM COMMENT c
                               JOIN USER u ON c.USER_ID = u.USER_ID
                               WHERE c.PRODUCT_ID = :id
                               ORDER BY c.CREATED_AT DESC
                               LIMIT 2");
    $comments->execute(['id' => $id]);

    foreach ($comments as $comment) {
        echo '<div class="review">
                <div class="stars">★★★★★</div>
                <p>' . htmlspecialchars($comment['COMMENT_TEXT']) . '</p>
                <div class="review-footer">
                    <img src="' . htmlspecialchars($comment['USER_IMG']) . '" alt="Autor">
                    <div>
                        <small>' . htmlspecialchars($comment['USER_NAME']) . '</small><br>
                        <small>' . date('d/m/Y', strtotime($comment['CREATED_AT'])) . '</small>
                    </div>
                </div>
              </div>';
    }

    echo '  </div>
          </div>
        </div>';

} catch (PDOException $e) {
    echo '<p>Erro: ' . $e->getMessage() . '</p>';
}
?>
