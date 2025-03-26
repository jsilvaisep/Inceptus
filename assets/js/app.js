// ==========================
// SPA: Navegação de Páginas
// ==========================
function loadPage(page, search = '') {
    let url = 'pages/' + page + '.php';
    if (search) url += '?' + search;

    fetch(url)
        .then(response => {
            if (!response.ok) throw new Error('Erro ao carregar');
            return response.text();
        })
        .then(html => {
            document.getElementById('content').innerHTML = html;

            const newUrl = '?page=' + page + (search ? '&' + search : '');
            history.pushState(null, '', newUrl);

            if (['login', 'register', 'home', 'profile', 'empresas'].includes(page)) {
                reloadNavbar();
            }

            setupPageScripts(page);
        })
        .catch(() => {
            document.getElementById('content').innerHTML = '<h3>Página não encontrada.</h3>';
        });
}

// ==========================
// SPA: Atualiza Navbar
// ==========================
function reloadNavbar() {
    fetch('includes/navbar.php')
        .then(res => res.text())
        .then(html => {
            document.querySelector('.main-header').outerHTML = html;
        });
}

// ==========================
// SPA: Scripts locais da página carregada
// ==========================
function setupPageScripts(page) {
    if (page === 'empresas') {
        const input = document.getElementById('company-search');
        if (input) {
            input.addEventListener('input', filterCompanies);
        }
    }
}

// ==========================
// SPA: Filtro de empresas no DOM
// ==========================
function filterCompanies() {
    const input = document.getElementById('company-search');
    if (!input) return;

    const term = input.value.toLowerCase();
    const cards = document.querySelectorAll('.company-card');
    let visibleCount = 0;

    cards.forEach(card => {
        const name = card.querySelector('h3').textContent.toLowerCase();
        if (name.includes(term)) {
            card.style.display = 'block';
            visibleCount++;
        } else {
            card.style.display = 'none';
        }
    });

    const noResults = document.querySelector('.no-results');
    if (noResults) {
        noResults.style.display = (visibleCount === 0) ? 'block' : 'none';
    }
}

// ==========================
// SPA: Inicialização e eventos
// ==========================
window.addEventListener('DOMContentLoaded', () => {
    const params = new URLSearchParams(window.location.search);
    const page = params.get('page') || 'home';
    params.delete('page');
    const search = params.toString();
    loadPage(page, search);

    // Navegação por clique em links
    document.addEventListener('click', e => {
        const link = e.target.closest('a');
        if (link && link.href.includes('?page=')) {
            e.preventDefault();
            const urlParams = new URL(link.href).searchParams;
            const pageParam = urlParams.get('page') || 'home';
            urlParams.delete('page');
            const searchParam = urlParams.toString();
            loadPage(pageParam, searchParam);
        }

        // Logout SPA
        if (e.target.id === 'logout-link') {
            e.preventDefault();
            fetch('pages/logout.php')
                .then(res => res.json())
                .then(() => {
                    loadPage('login');
                    reloadNavbar();
                });
        }
    });

    // Submissão de formulários SPA
    document.addEventListener('submit', e => {
        const form = e.target;

        // Pesquisa (Home e Empresas)
        if (form.classList.contains('search-box')) {
            e.preventDefault();
            const input = form.querySelector('input[name="search"]');
            const term = input.value.trim();
            const page = form.dataset.page || 'home';
            loadPage(page, `search=${encodeURIComponent(term)}`);
        }

        // Registo
        if (form.id === 'register-form') {
            e.preventDefault();
            const formData = new FormData(form);
            const msg = document.getElementById('register-msg');

            if (!form.querySelector('#terms').checked) {
                msg.innerHTML = `<p class="error">Aceite os termos e condições.</p>`;
                return;
            }

            fetch('pages/register.php', {
                method: 'POST',
                body: formData
            })
                .then(res => res.json())
                .then(data => {
                    msg.innerHTML = `<p class="${data.success ? 'success' : 'error'}">${data.message}</p>`;
                    if (data.success) {
                        form.reset();
                        setTimeout(() => {
                            loadPage('login');
                            reloadNavbar();
                        }, 1500);
                    }
                })
                .catch(() => {
                    msg.innerHTML = `<p class="error">Erro no servidor.</p>`;
                });
        }

        // Login
        if (form.id === 'login-form') {
            e.preventDefault();
            const formData = new FormData(form);
            const msg = document.getElementById('login-msg');

            fetch('pages/login.php', {
                method: 'POST',
                body: formData
            })
                .then(res => res.json())
                .then(data => {
                    msg.innerHTML = `<p class="${data.success ? 'success' : 'error'}">${data.message}</p>`;
                    if (data.success) {
                        setTimeout(() => {
                            loadPage('home');
                            reloadNavbar();
                        }, 1000);
                    }
                })
                .catch(() => {
                    msg.innerHTML = `<p class="error">Erro no servidor.</p>`;
                });
        }

        // Edição de perfil
        if (form.id === 'edit-profile-form') {
            e.preventDefault();
            const formData = new FormData(form);
            const msg = document.getElementById('profile-msg');

            fetch('pages/profile.php', {
                method: 'POST',
                body: formData
            })
                .then(res => res.json())
                .then(data => {
                    msg.innerHTML = `<p class="${data.success ? 'success' : 'error'}">${data.message}</p>`;
                    if (data.success) reloadNavbar();
                })
                .catch(() => {
                    msg.innerHTML = `<p class="error">Erro ao atualizar perfil.</p>`;
                });
        }
    });
});
