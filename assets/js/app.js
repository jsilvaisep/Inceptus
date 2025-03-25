// SPA: Navegação e submissão com AJAX
function loadPage(page, search = '') {
    let url = 'pages/' + page + '.php';
    if (search) {
        url += '?search=' + encodeURIComponent(search);
    }

    fetch(url)
        .then(response => {
            if (!response.ok) throw new Error('Erro ao carregar');
            return response.text();
        })
        .then(html => {
            document.getElementById('content').innerHTML = html;
            const newUrl = '?page=' + page + (search ? '&search=' + encodeURIComponent(search) : '');
            history.pushState(null, '', newUrl);
        })
        .catch(error => {
            document.getElementById('content').innerHTML = '<h3>Página não encontrada.</h3>';
        });
}

window.addEventListener('DOMContentLoaded', () => {
    const params = new URLSearchParams(window.location.search);
    const page = params.get('page') || 'home';
    const search = params.get('search') || '';
    loadPage(page, search);

    document.addEventListener('click', e => {
        if (e.target.closest('.navbar a')) {
            e.preventDefault();
            const page = e.target.getAttribute('href').split('=')[1];
            loadPage(page);
        }
    });

    document.addEventListener('submit', function(e) {
        const form = e.target;

        if (form.classList.contains('search-box')) {
            e.preventDefault();
            const input = form.querySelector('input[name="search"]');
            const term = input.value.trim();
            loadPage('home', term);
        }

        if (form.id === 'register-form') {
            e.preventDefault();
            const formData = new FormData(form);
            fetch('auth/register_process.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                const msg = document.getElementById('register-msg');
                if (data.success) {
                    msg.innerHTML = `<p class="success">${data.message}</p>`;
                    form.reset();
                } else {
                    msg.innerHTML = `<p class="error">${data.message}</p>`;
                }
            })
            .catch(err => {
                document.getElementById('register-msg').innerHTML = `<p class="error">Erro no servidor.</p>`;
            });
        }

        if (form.id === 'login-form') {
            e.preventDefault();
            const formData = new FormData(form);
            fetch('auth/login_process.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                const msg = document.getElementById('login-msg');
                if (data.success) {
                    msg.innerHTML = `<p class="success">${data.message}</p>`;
                    setTimeout(() => {
                        loadPage('home');
                    }, 1000);
                } else {
                    msg.innerHTML = `<p class="error">${data.message}</p>`;
                }
            })
            .catch(err => {
                document.getElementById('login-msg').innerHTML = `<p class="error">Erro no servidor.</p>`;
            });
        }
    });
});