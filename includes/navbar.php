<?php session_start(); ?>
<header class="main-header">
    <div class="container">
        <div class="logo">
            <a href="?page=home" onclick="loadPage('home'); return false;">
                <img src="assets/img/logo.png" alt="Logo Inceptus" class="logo-img">
            </a>
        </div>

        <nav class="navbar">
            <ul>
                <li><a href="?page=produtos" class="nav-link" onclick="loadPage('produtos'); return false;">Produtos</a></li>
                <li><a href="?page=empresas" class="nav-link" onclick="loadPage('empresas'); return false;">Empresas</a></li>
                <li><a href="?page=noticias" class="nav-link" onclick="loadPage('noticias'); return false;">Notícias</a></li>
                <li><a href="?page=warroom" class="nav-link" onclick="loadPage('warroom'); return false;">Warroom</a></li>

                <?php if (isset($_SESSION['user'])): ?>
                    <li class="user-dropdown">
                        <button class="dropdown-toggle">
                            <img src="<?= !empty($_SESSION['user']['user_img']) ? $_SESSION['user']['user_img'] : 'assets/img/default-user.png' ?>" alt="Avatar" class="avatar">
                            <span><?= htmlspecialchars($_SESSION['user']['user_name']) ?></span>
                        </button>
                        <div class="dropdown-menu">
                            <a href="?page=profile" onclick="loadPage('profile'); return false;">👤 Perfil</a>
                            <a href="?page=admin/dashboard" onclick="loadPage('admin/dashboard'); return false;">⚙️ Dashboard</a>
                            <a href="#" id="logout-link">🚪 Logout</a>
                        </div>
                    </li>

                    <script>
                        document.getElementById('logout-link').addEventListener('click', function(event) {
                            event.preventDefault();
                            if (confirm('Você tem certeza que deseja sair?')) {
                                fetch('pages/logout.php')
                                    .then(response => {
                                        return response.json();
                                    })
                                    .then(data => {
                                        if (data.success) {
                                            loadPage('login');
                                            reloadNavbar();
                                        } else {
                                            alert('Erro ao fazer logout');
                                        }
                                    })
                                    .catch(() => {
                                        alert('Erro ao fazer logout');
                                    });
                            }
                        });
                    </script>
                <?php else: ?>
                    <li><a href="?page=login" onclick="loadPage('login'); return false;">Login</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>

    <script>
        // SPA: Atualiza a link ativo na navbar
        function updateActiveLink() {
            const currentPage = new URLSearchParams(window.location.search).get('page');
            const navLinks = document.querySelectorAll('.nav-link');
            navLinks.forEach(link => link.classList.remove('active'));
            navLinks.forEach(link => {
                if (link.href.includes(`?page=${currentPage}`)) {
                    link.classList.add('active');
                }
            });
        }

        // Observa a mudança da URL para ajustar a navegação sem reload
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

        // Ao carregar o conteúdo da página, atualiza a navbar e os links ativos
        document.addEventListener("DOMContentLoaded", () => {
            updateActiveLink();
            observeUrlChange();
        });

        // Atualiza a navbar sem recarregar a página
        function reloadNavbar() {
            fetch('includes/navbar.php')
                .then(res => res.text())
                .then(html => {
                    document.querySelector('.main-header').outerHTML = html;
                });
        }
    </script>
</header>
