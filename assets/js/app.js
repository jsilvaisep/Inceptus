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
            history.replaceState(null, '', '?page=' + page + (search ? '&' + search : ''));
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
    const input = document.getElementById('company-search');
    if (page === 'empresas' && input) {
        input.addEventListener('input', filterCompanies);
    }

    document.querySelectorAll('.clickable-card').forEach(card => {
        card.addEventListener('click', () => {
            const id = card.dataset.id;
            const modalPage = page === 'produtos' ? 'produtos' : 'empresas';
            fetch(`pages/${modalPage}.php?id=${id}&modal=true`)
                .then(res => res.text())
                .then(html => {
                    const container = document.getElementById('modal-container');
                    if (container) container.innerHTML = html;
                    document.body.classList.add('no-scroll');
                    setupGlobalModalListeners();
                });
        });
    });

    if (page === 'home') {
        initNewsCarousel();
    }

    // Setup login form listener
    const loginForm = document.getElementById('login-form');
    if (page === 'login' && loginForm) {
        loginForm.addEventListener('submit', e => {
            e.preventDefault();
            const formData = new FormData(loginForm);
            const msg = document.getElementById('login-msg');

            fetch('pages/login.php', { method: 'POST', body: formData })
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
        });
    }

    // Setup register form listener
    if (page === 'register') {
        const registerForm = document.getElementById('register-form');
        if (registerForm) {
            registerForm.addEventListener('submit', async e => {
                e.preventDefault();
                const formData = new FormData(registerForm);
                const msg = document.getElementById('register-msg');
                msg.innerHTML = 'A processar...';

                try {
                    const res = await fetch('pages/register.php', {
                        method: 'POST',
                        body: formData
                    });
                    const data = await res.json();
                    msg.innerHTML = `<p class="${data.success ? 'success' : 'error'}">${data.message}</p>`;

                    if (data.success) {
                        setTimeout(() => {
                            loadPage('home');
                            reloadNavbar();
                        }, 2000);
                    }
                } catch {
                    msg.innerHTML = `<p class="error">Erro no servidor.</p>`;
                }
            });
        }
    }

    setupGlobalModalListeners();
}

// ==========================
// SPA: Modal handler global
// ==========================
function setupGlobalModalListeners() {
    document.querySelectorAll('.modal-overlay, .modal-close').forEach(el => {
        el.addEventListener('click', closeModal);
    });

    const newsModal = document.getElementById('post-modal');
    if (newsModal) {
        newsModal.addEventListener('click', e => {
            if (e.target === newsModal) closeModal();
        });

        const closeBtn = newsModal.querySelector('.news-modal-close');
        if (closeBtn) {
            closeBtn.addEventListener('click', closeModal);
        }
    }
}

function closeModal() {
    const modalContainer = document.getElementById('modal-container');
    const postModal = document.getElementById('post-modal');
    if (modalContainer) modalContainer.innerHTML = '';
    if (postModal) postModal.style.display = 'none';
    document.body.classList.remove('no-scroll');
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
// SPA: Inicialização
// ==========================
window.addEventListener('DOMContentLoaded', () => {
    const params = new URLSearchParams(window.location.search);
    const page = params.get('page') || 'home';
    params.delete('page');
    const search = params.toString();
    loadPage(page, search);

    document.body.addEventListener('click', e => {
        const link = e.target.closest('a');
        if (link && link.href.includes('?page=')) {
            e.preventDefault();
            const urlParams = new URL(link.href).searchParams;
            const pageParam = urlParams.get('page') || 'home';
            urlParams.delete('page');
            const searchParam = urlParams.toString();
            loadPage(pageParam, searchParam);
        }

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
});

// ==========================
// Banner de Notícias (News)
// ==========================
function initNewsCarousel() {
    let currentNews = 0;
    const slides = document.querySelectorAll('.news-slide');
    if (!slides.length) return;

    const showSlide = index => {
        slides.forEach(s => s.classList.remove('active'));
        slides[index].classList.add('active');
    };

    const nextSlide = () => {
        currentNews = (currentNews + 1) % slides.length;
        showSlide(currentNews);
    };

    const prevSlide = () => {
        currentNews = (currentNews - 1 + slides.length) % slides.length;
        showSlide(currentNews);
    };

    document.querySelector('.news-nav.next')?.addEventListener('click', nextSlide);
    document.querySelector('.news-nav.prev')?.addEventListener('click', prevSlide);

    let autoSlide = setInterval(nextSlide, 7000);
    const container = document.querySelector('.news-carousel-container');
    if (container) {
        container.addEventListener('mouseenter', () => clearInterval(autoSlide));
        container.addEventListener('mouseleave', () => autoSlide = setInterval(nextSlide, 7000));
    }

    slides.forEach(slide => {
        const button = slide.querySelector('.read-more-btn');
        if (button) {
            button.addEventListener('click', () => {
                const title = slide.dataset.title;
                const content = slide.dataset.content;
                const date = slide.dataset.date;
                const img = slide.dataset.img;

                document.getElementById("modal-title").innerText = title;
                document.getElementById("modal-text").innerText = content;
                document.getElementById("modal-date").innerText = '🕒 ' + date;
                document.getElementById("modal-img").src = img;
                document.getElementById("post-modal").style.display = "flex";
            });
        }
    });

    showSlide(currentNews);
}
