<?php
session_start();
include 'db.php';

$userName = '';
$userImg = 'assets/img/default-user.png';
$isAdmin = false;

if (isset($_SESSION['user'])) {
    $userId = hex2bin($_SESSION['user']['user_id'] ?? '');
    $userName = $_SESSION['user']['user_name'] ?? '';
    $imgFromSession = $_SESSION['user']['img_url'] ?? '';

    if (!empty($imgFromSession)) {
        $userImg = htmlspecialchars($imgFromSession);
    }

    $stmt = $pdo->prepare("
        SELECT u.USER_TYPE_ID, ut.USER_TYPE
        FROM USER u
        JOIN USER_TYPE ut ON u.USER_TYPE_ID = ut.TYPE_ID
        WHERE u.USER_ID = ?
    ");
    $stmt->execute([$userId]);
    $typeData = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($typeData && $typeData['USER_TYPE'] === 'ADMIN') {
        $isAdmin = true;
    }
}
?>

<header class="main-header">
  <div class="container">
    <div class="logo">
      <a href="?page=home">
        <img src="assets/img/logo.png" alt="Logo Inceptus" class="logo-img">
      </a>
    </div>

    <nav class="navbar">
      <ul>
        <li><a href="?page=produtos" class="nav-link">Produtos</a></li>
        <li><a href="?page=empresas" class="nav-link">Empresas</a></li>
        <li><a href="?page=noticias" class="nav-link">Notícias</a></li>
        <li><a href="?page=warroom" class="nav-link">Warroom</a></li>

        <?php if (!empty($userName)): ?>
        <li class="user-dropdown">
          <button class="dropdown-toggle">
            <img src="<?= $userImg ?>" alt="Avatar" class="avatar">
            <span><?= htmlspecialchars($userName) ?></span>
            <svg class="chevron" width="12" height="12" viewBox="0 0 320 512">
              <path fill="currentColor"
                d="M31.3 192l128 128 128-128c9.4-9.4 24.6-9.4 33.9 0s9.4 24.6 0 33.9l-144 144c-9.4 9.4-24.6 9.4-33.9 0l-144-144c-9.4-9.4-9.4-24.6 0-33.9s24.6-9.4 33.9 0z" />
            </svg>
          </button>
          <div class="dropdown-menu">
            <a href="?page=profile">👤 Perfil</a>
            <?php if ($isAdmin): ?>
              <a href="?page=admin/dashboard">⚙️ Dashboard</a>
            <?php endif; ?>
            <a href="#" id="logout-link">🚪 Logout</a>
          </div>
        </li>
        <?php else: ?>
        <li><a href="?page=login" class="login-btn">Login</a></li>
        <?php endif; ?>
      </ul>
    </nav>
  </div>
</header>
