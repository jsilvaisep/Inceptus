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
                <li><a href="?page=produtos" class="nav-link">Produtos</a></li>
                <li><a href="?page=empresas" class="nav-link">Empresas</a></li>
                <li><a href="?page=noticias" class="nav-link">Notícias</a></li>
                <li><a href="?page=forums" class="nav-link">Forums</a></li>
                <li><a href="?page=warroom" class="nav-link">Warroom</a></li>

                <?php if (isset($_SESSION['user'])): ?>
                    <li class="user-dropdown">
                        <button class="dropdown-toggle">
                            <img src="<?= !empty($_SESSION['user']['user_img']) ? htmlspecialchars($_SESSION['user']['user_img']) : 'assets/img/default-user.png' ?>" alt="Avatar" class="avatar">
                            <span><?= htmlspecialchars($_SESSION['user']['user_name']) ?></span>
                            <svg class="chevron" width="12" height="12" viewBox="0 0 320 512">
                                <path fill="currentColor" d="M31.3 192l128 128 128-128c9.4-9.4 24.6-9.4 33.9 0s9.4 24.6 0 33.9l-144 144c-9.4 9.4-24.6 9.4-33.9 0l-144-144c-9.4-9.4-9.4-24.6 0-33.9s24.6-9.4 33.9 0z"/>
                            </svg>
                        </button>
                        <div class="dropdown-menu">
                            <a href="?page=profile"><span>👤</span> Perfil</a>
                            <?php if ($_SESSION['user']['user_type'] === 'ADMIN'): ?>
                                <a class="dropdown-item" href="?page=admin/dashboard" id="adminConfigBtn">
                                    <i class="fas fa-cog"><span>⚙️</span></i> Dashboard
                                </a>
                            <?php endif; ?>
                            <a href="#" id="logout-link"><span>🚪</span> Logout</a>
                        </div>
                    </li>
                <?php else: ?>
                    <li><a href="?page=login">Login</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
    <script>
        function updateActiveLink() {
            const currentUrl = window.location.href;
            const navLinks = document.querySelectorAll('.nav-link');
            navLinks.forEach(link => link.classList.remove('active'));
            navLinks.forEach(link => {
                if (currentUrl.includes(link.getAttribute('href'))) {
                    link.classList.add('active');
                }
            });
        }
        function observeUrlChange() {
            let lastUrl = window.location.href;
            new MutationObserver(() => {
                const currentUrl = window.location.href;
                if (currentUrl !== lastUrl) {
                    lastUrl = currentUrl;
                    updateActiveLink();
                }
            }).observe(document, { subtree: true, childList: true });
        }
        document.addEventListener("DOMContentLoaded", () => {
            updateActiveLink();
            observeUrlChange();
        });
        window.addEventListener("popstate", updateActiveLink);
    </script>
</header>