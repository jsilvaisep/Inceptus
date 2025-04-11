<?php include_once '../includes/db.php';
session_start();
if (isset($_SESSION['user'])) {
    $userID = $_SESSION['user']['user_id'];
} else {
    header("Location: /pages/redirect.php");
    exit;
}

$page = isset($_GET['pg']) ? (int) $_GET['pg'] : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Construir a query base
$sql = "SELECT p.POST_ID, p.POST_CONTENT, p.CREATED_AT,
    c.COMPANY_NAME, c.IMG_URL, u.USER_NAME
    FROM POST p
    INNER JOIN COMPANY c ON c.COMPANY_ID = p.COMPANY_ID
    INNER JOIN USER u ON u.USER_ID = c.USER_ID
    WHERE p.POST_STATUS = 'A'";

// Adicionar condição de pesquisa se existir um termo
if (!empty($search)) {
    $sql .= " AND (p.POST_CONTENT LIKE :search 
              OR c.COMPANY_NAME LIKE :search 
              OR u.USER_NAME LIKE :search)";
}

// Adicionar ordenação e limites
$sql .= " ORDER BY p.CREATED_AT DESC LIMIT :limit OFFSET :offset";

$stmt = $pdo->prepare($sql);
if (!empty($search)) {
    $searchParam = "%{$search}%";
    $stmt->bindParam(':search', $searchParam, PDO::PARAM_STR);
}
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$posts = $stmt->fetchAll();

// Consulta de contagem também precisa incluir o filtro de pesquisa
$countSql = "SELECT COUNT(*) FROM POST p 
             INNER JOIN COMPANY c ON c.COMPANY_ID = p.COMPANY_ID
             INNER JOIN USER u ON u.USER_ID = c.USER_ID
             WHERE p.POST_STATUS = 'A'";

if (!empty($search)) {
    $countSql .= " AND (p.POST_CONTENT LIKE :search 
                  OR c.COMPANY_NAME LIKE :search 
                  OR u.USER_NAME LIKE :search)";
}

$countStmt = $pdo->prepare($countSql);
if (!empty($search)) {
    $countStmt->bindParam(':search', $searchParam, PDO::PARAM_STR);
}

$countStmt->execute();
$totalPosts = $countStmt->fetchColumn();
$totalPages = ceil($totalPosts / $limit);
?>

<div class="wrapper">
    <div class="news-container">
        <div class="news-sidebar">
            <?php include_once '../includes/filter.php'; ?>
        </div>
        <div class="news-page">
            <div class="news-grid">
                <?php foreach ($posts as $row): ?>
                    <?php
                        $truncatedContent = strlen($row['POST_CONTENT']) > 100 ?
                            substr($row['POST_CONTENT'], 0, 100) . '...' :
                            $row['POST_CONTENT'];

                        $date = new DateTime($row['CREATED_AT']);
                        $formattedDate = $date->format('d/m/Y H:i');
                    ?>
                    <div class="news-card" data-id="<?= htmlspecialchars($row['POST_ID']) ?>">
                        <div class="news-card-header">
                            <img src="<?= htmlspecialchars($row['IMG_URL']) ?>" alt="<?= htmlspecialchars($row['COMPANY_NAME']) ?>" class="company-logo">
                            <div class="meta-info">
                                <h3><?= htmlspecialchars($row['COMPANY_NAME']) ?></h3>
                                <span class="news-date">🕒 <?= $formattedDate ?></span>
                            </div>
                        </div>
                        <div class="news-card-content">
                            <p class="news-text"><?= htmlspecialchars($truncatedContent) ?></p>
                        </div>
                        <div class="news-card-footer">
                            <button class="open-modal-btn" onclick="window.location.href='?page=noticiacompleta&id=<?= htmlspecialchars($row['POST_ID']) ?>'">Ler mais</button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="pagination">
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <a href="?page=noticias&pg=<?= $i ?>" class="page-link <?= ($i == $page) ? 'active' : '' ?>"> <?= $i ?> </a>
                <?php endfor; ?>
            </div>
        </div>
    </div>
</div>