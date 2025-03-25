// SPA: Navegação e submissão com AJAX
function loadPage(page, search = '') {
    let url = 'pages/' + page + '.php';
    if (search) url += '?search=' + encodeURIComponent(search);

    fetch(url)
        .then(response => {
            if (!response.ok) throw new Error('Erro ao carregar');
            return response.text();
        })
        .then(html => {
            document.getElementById('content').innerHTML = html;

            const newUrl = '?page=' + page + (search ? '&search=' + encodeURIComponent(search) : '');
            history.pushState(null, '', newUrl);

            // Recarrega navbar se for login, logout ou register
            if (['login', 'register', 'home', 'profile'].includes(page)) {
                reloadNavbar();
            }
        })
        .catch(() => {
            document.getElementById('content').innerHTML = '<h3>Página não encontrada.</h3>';
        });
}

function reloadNavbar() {
    fetch('includes/navbar.php')
        .then(res => res.text())
        .then(html => {
            document.querySelector('.main-header').outerHTML = html;
        });
}

window.addEventListener('DOMContentLoaded', () => {
    const params = new URLSearchParams(window.location.search);
    const page = params.get('page') || 'home';
    const search = params.get('search') || '';
    loadPage(page, search);

    // Navegação SPA ao clicar em links
    document.addEventListener('click', e => {
        const link = e.target.closest('a');

        // Links com ?page=
        if (link && link.href.includes('?page=')) {
            e.preventDefault();
            const page = link.href.split('?page=')[1].split('&')[0];
            loadPage(page);
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

    // Submissões de formulários SPA
    document.addEventListener('submit', e => {
        const form = e.target;

        // Pesquisa
        if (form.classList.contains('search-box')) {
            e.preventDefault();
            const input = form.querySelector('input[name="search"]');
            const term = input.value.trim();
            loadPage('home', term);
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
                    if (data.success) {
                        reloadNavbar();
                    }
                })
                .catch(() => {
                    msg.innerHTML = `<p class="error">Erro ao atualizar perfil.</p>`;
                });
        }
    });
});
