<?php
require_once __DIR__ . '/../includes/db.php';

session_start();

if (!isset($_SESSION['user'])) {
    http_response_code(403);
    echo "⚠️ Acesso negado. Por favor inicie sessão.";
    exit;
}

if (!isset($_GET['id'])) {
    echo "<h3>Notícia não encontrada.</h3>";
    return;
}

$postId = $_GET['id'];

$stmt = $pdo->prepare("SELECT p.POST_ID, p.POST_CONTENT, p.TITLE, p.SUBTITLE, p.CREATED_AT, c.COMPANY_NAME, c.IMG_URL, u.USER_NAME FROM POST p
    INNER JOIN COMPANY c ON p.COMPANY_ID = c.COMPANY_ID
    INNER JOIN USER u ON c.USER_ID = u.USER_ID
    WHERE p.POST_ID = ?");
$stmt->execute([$postId]);
$noticia = $stmt->fetch();

if (!$noticia) {
    echo "<h3>Notícia não encontrada.</h3>";
    return;
}

// Handle AJAX POST comment
$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

if ($isAjax && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['resposta'])) {
    $resposta = trim($_POST['resposta']);
    $userId = $_SESSION['user']['user_id'];

    if (!empty($resposta)) {
        $stmt = $pdo->prepare("CALL INSERT_POST_EXT (?, ?, ?)");
        $stmt->execute([$postId, $userId, $resposta]);
    }

    // Return updated comment section only
    $stmt = $pdo->prepare("SELECT pe.POST_EXT_CONTENT, pe.CREATED_AT, u.USER_NAME
        FROM POST_EXT pe
        INNER JOIN USER u ON pe.USER_ID = u.USER_ID
        WHERE pe.POST_ID = ?
        ORDER BY pe.CREATED_AT DESC");
    $stmt->execute([$postId]);
    $comentarios = $stmt->fetchAll();
    foreach ($comentarios as $comentario):
        $date = new DateTime($comentario['CREATED_AT']);
        $formattedDate = $date->format('d/m/Y H:i');?>
        <div class="news-card comment">
            <div class="news-card-content">
                <p class="news-text"><?= nl2br(htmlspecialchars($comentario['POST_EXT_CONTENT'])) ?></p>
            </div>
            <div class="news-card-footer">
                <span class="news-author">👤 <?= htmlspecialchars($comentario['USER_NAME']) ?></span>
                <span class="news-date">🕒 <?= $formattedDate ?></span>
            </div>
        </div>
    <?php endforeach;
    return;
}

$stmt = $pdo->prepare("SELECT pe.POST_EXT_CONTENT, pe.CREATED_AT, u.USER_NAME
    FROM POST_EXT pe
    INNER JOIN USER u ON pe.USER_ID = u.USER_ID
    WHERE pe.POST_ID = ?
    ORDER BY pe.CREATED_AT DESC");
$stmt->execute([$postId]);
$comentarios = $stmt->fetchAll();
?>

<div class="wrapper">
    <div class="news-container full">
        <div class="news-page full">
            <div class="news-article">
                <h1 class="news-title"><?= htmlspecialchars($noticia['TITLE']) ?></h1>
                <h2 class="news-subtitle"><?= htmlspecialchars($noticia['SUBTITLE']) ?></h2>

                <div class="news-meta">
                    <img src="<?= htmlspecialchars($noticia['IMG_URL']) ?>" alt="<?= htmlspecialchars($noticia['COMPANY_NAME']) ?>" class="company-logo">
                    <div class="meta-info">
                        <span class="company-name"><?= htmlspecialchars($noticia['COMPANY_NAME']) ?></span>
                        <span class="news-date">🕒 <?= htmlspecialchars($noticia['CREATED_AT']) ?></span>
                    </div>
                </div>

                <div class="news-body">
                    <p><?= nl2br(htmlspecialchars($noticia['POST_CONTENT'])) ?></p>
                </div>
            </div>

            <div class="comentarios">
                <h4>Comentários:</h4>
                <div id="comment-section">
                    <?php foreach ($comentarios as $comentario):
                        $date = new DateTime($comentario['CREATED_AT']);
                        $formattedDate = $date->format('d/m/Y H:i');?>
                        <div class="news-card comment">
                            <div class="news-card-content">
                                <p class="news-text"><?= nl2br(htmlspecialchars($comentario['POST_EXT_CONTENT'])) ?></p>
                            </div>
                            <div class="news-card-footer">
                                <span class="news-author">👤 <?= htmlspecialchars($comentario['USER_NAME']) ?></span>
                                <span class="news-date">🕒 <?= $formattedDate ?></span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <form>
                    <div class="comment_form">
                        <h2>Comentário</h2>
                        <textarea class="text-comment" id="post_response<?= $noticia['POST_ID'] ?>" placeholder="Escreva o seu comentário..." rows="4"></textarea>
                        <div class="comment_actions">
                            <button class="botao-comentar" type="button" onclick="submitComentarioNoticia('<?= $noticia['POST_ID'] ?>')">Comentar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
