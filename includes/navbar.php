<?php session_start(); ?>
<header class="main-header">
    <div class="container">
    <div class="logo">
    <a href="?page=home">
        <img src="assets/img/logo.png" alt="Logo Inceptus" class="logo-img">
    </a>
</div>


        <nav class="navbar">
            <ul>
                <li><a href="?page=home">Home</a></li>
                <li><a href="?page=produtos">Produtos</a></li>
                <li><a href="?page=empresas">Empresas</a></li>
                <li><a href="?page=noticias">Notícias</a></li>
                <li><a href="?page=forums">Forums</a></li>
                <li><a href="?page=warroom">Warroom</a></li>

                <?php if (isset($_SESSION['user_id'])): ?>
                    <li class="user-dropdown">
                        <button class="dropdown-toggle">
                            <img src="<?= !empty($_SESSION['user_img']) ? htmlspecialchars($_SESSION['user_img']) : 'assets/img/default-user.png' ?>" alt="Avatar" class="avatar">
                            <span><?= htmlspecialchars($_SESSION['user_name']) ?></span>
                            <svg class="chevron" width="12" height="12" viewBox="0 0 320 512"><path fill="currentColor" d="M31.3 192l128 128 128-128c9.4-9.4 24.6-9.4 33.9 0s9.4 24.6 0 33.9l-144 144c-9.4 9.4-24.6 9.4-33.9 0l-144-144c-9.4-9.4-9.4-24.6 0-33.9s24.6-9.4 33.9 0z"/></svg>
                        </button>
                        <div class="dropdown-menu">
                            <a href="?page=profile"><span>👤</span> Perfil</a>
                            <a href="?page=settings"><span>⚙️</span> Configurações</a>
                            <a href="#" id="logout-link"><span>🚪</span> Logout</a>
                        </div>
                    </li>
                <?php else: ?>
                    <li><a href="?page=login">Login</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
</header>
