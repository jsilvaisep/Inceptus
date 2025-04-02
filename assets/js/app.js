// ==========================
// Renderiza as páginas sem ter que recarregar todo o código. 
// Garante que o que temos é um SPA.
// o fetch faz o carregamento da página.
// O setupPageScripts vai buscar o que é suposto apresentar.
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

function criarProduto(){
    document.getElementById("openModal").addEventListener("click", function() {
        document.getElementById("modalOverlay").style.display = "flex";
    });
    
    document.getElementById("closeModal").addEventListener("click", function() {
        document.getElementById("modalOverlay").style.display = "none";
    });
    
}


//Recebe o form e verifica os campos ao criar Produto
document.addEventListener("submit", function () {
        let name = document.getElementById("product_name").value.trim();
        let description = document.getElementById("product_description").value.trim();
        let category = document.getElementById("category_id").value;
        let company = "4ce516e6-0be9-11f0-b0d3-020017000d59";
        alert(name);

        if (!name || !description || !category || !company) {
            alert("Todos os campos são obrigatórios.");
        }
    });


// ==========================
// Atualiza a barra de navegação, mantendo a lógica do SPA, sem recarregar a página inteira.
// ==========================
function reloadNavbar() {
    fetch('includes/navbar.php')
        .then(res => res.text())
        .then(html => {
            document.querySelector('.main-header').outerHTML = html;
        });
}

// ==========================
// Fecha algum modal que esteja aberto.
// ==========================
function closeModal() {
    document.getElementById('modal-container').innerHTML = '';
}

// ==========================
// Para alternar entre mostrar todo o texto ou apenas um pouco.
// Não será utilizado, mas deixo comentado caso seja necessário voltar a usar algo semelhante.
// ==========================
// function toggleDescription(button) {
//     const container = button.closest('.product-description');
//     const shortText = container.querySelector('.description-text');
//     const fullText = container.querySelector('.full-description');

//     if (fullText.style.display === 'none') {
//         shortText.style.display = 'none';
//         fullText.style.display = 'block';
//         button.textContent = 'Menos...';
//     } else {
//         shortText.style.display = 'block';
//         fullText.style.display = 'none';
//         button.textContent = 'Mais...';
//     }
// }

// ==========================
// SPA: Scripts locais da página carregada
// ==========================
function setupPageScripts(page) {
    // Pesquisa AJAX dinâmica para produtos e empresas
    if (page === 'produtos') {
        const searchInput = document.getElementById('search-input');
        const resultsDiv = document.getElementById('search-results');
        const starContainer = document.getElementById('stars');
        const toggleInputs = document.querySelectorAll('#projectToggle input[name="type"]');
        const tagSection = document.querySelector('.filter-section.tags');
        const minViewsInput = document.getElementById('min-views');
        const maxViewsInput = document.getElementById('max-views');

        let minViews = new URLSearchParams(window.location.search).get('min_views');
        let maxViews = new URLSearchParams(window.location.search).get('max_views');

        if (minViewsInput && maxViewsInput) {
            if (minViews) minViewsInput.value = minViews;
            if (maxViews) maxViewsInput.value = maxViews;
        
            // Adiciona listeners aos inputs
            minViewsInput.addEventListener('input', triggerViewsFilter);
            maxViewsInput.addEventListener('input', triggerViewsFilter);
        }

        function triggerViewsFilter() {
            const min = minViewsInput.value;
            const max = maxViewsInput.value;
        
            if (min && max) {
                minViews = min;
                maxViews = max;
                loadWithFilters();
            } 
        }
        

        if(tagSection) {
            tagSection.remove();
        }

        let selectedRank = parseInt(new URLSearchParams(window.location.search).get('rank')) || null;

        // 🌟 ESTRELAS
        if (starContainer) {
            const stars = starContainer.querySelectorAll('.star');

            // Aplicar visual ativo se URL já tiver rank
            if (selectedRank) highlightStars(selectedRank);

            stars.forEach(star => {
                star.addEventListener('click', () => {
                    const value = parseInt(star.getAttribute('data-value'));

                    if (selectedRank === value) {
                        selectedRank = null;
                        clearStars();
                    } else {
                        selectedRank = value;
                        highlightStars(value);
                    }

                    loadWithFilters();
                });
            });

            function highlightStars(value) {
                stars.forEach(s => {
                    const v = parseInt(s.getAttribute('data-value'));
                    s.classList.toggle('selected', v <= value);
                });
            }

            function clearStars() {
                stars.forEach(s => s.classList.remove('selected'));
            }
        }

        // 🔁 TOGGLE (produtos / ambos / projetos)
        toggleInputs.forEach(input => {
            input.addEventListener('change', () => {
                loadWithFilters();
            });
        });

        // 🔍 PESQUISA
        if (searchInput && resultsDiv) {
            let debounce;
            searchInput.addEventListener('input', () => {
                const query = searchInput.value.trim();
                clearTimeout(debounce);
                debounce = setTimeout(() => {
                    if (!query) {
                        resultsDiv.innerHTML = '';
                        return;
                    }

                    fetch(`includes/search-products.php?q=${encodeURIComponent(query)}`)
                        .then(res => res.text())
                        .then(html => {
                            resultsDiv.innerHTML = html;
                            document.querySelectorAll('.clickable-product').forEach(item => {
                                item.addEventListener('click', () => {
                                    const id = item.dataset.id;
                                    fetch(`pages/produtos.php?id=${id}&modal=true`)
                                        .then(res => res.text())
                                        .then(modalHtml => {
                                            document.getElementById("modal-container").innerHTML = modalHtml;
                                            document.body.classList.add("no-scroll");
                                            setupGlobalModalListeners();
                                            resultsDiv.innerHTML = '';
                                            searchInput.value = '';
                                        });
                                });
                            });
                        });
                }, 300);
            });

            searchInput.addEventListener('keydown', (e) => {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    loadWithFilters();
                }
            });
        }

        // 🧠 Função geral para construir a query e recarregar
        function loadWithFilters() {
            const type = document.querySelector('#projectToggle input[name="type"]:checked')?.value || 'both';
            const search = document.getElementById('search-input')?.value.trim() || '';
            const url = new URLSearchParams();

            if (search) url.set('search', search);
            if (type) url.set('type', type);
            if (selectedRank !== null) url.set('rank', selectedRank);
            if (minViews && maxViews) {
                url.set('min_views', minViews);
                url.set('max_views', maxViews);
            }
            url.set('pg', '1');

            loadPage('produtos', url.toString());
        }
    }

    // Empresas: Filtro no DOM (além do search)
    if (page === 'empresas') {
        filterCompanies();

        const searchInput = document.getElementById('search-input');
        const resultsDiv = document.getElementById('search-results');
        const starContainer = document.getElementById('stars');
        // 👁 FILTRO DE VIEWS
        const minViewsInput = document.getElementById('min-views');
        const maxViewsInput = document.getElementById('max-views');

        let minViews = new URLSearchParams(window.location.search).get('min_views');
        let maxViews = new URLSearchParams(window.location.search).get('max_views');

        // Preencher os inputs se vierem da URL
        if (minViewsInput && maxViewsInput) {
            if (minViews) minViewsInput.value = minViews;
            if (maxViews) maxViewsInput.value = maxViews;

            // Adiciona listeners aos inputs
            minViewsInput.addEventListener('input', triggerViewsFilter);
            maxViewsInput.addEventListener('input', triggerViewsFilter);
        }

        function triggerViewsFilter() {
            const min = minViewsInput.value;
            const max = maxViewsInput.value;
        
            if (min && max) {
                minViews = min;
                maxViews = max;
                loadWithFilters();
            }
        }
        

        const toggleContent = document.querySelector('.filter-section.custom-toggle-wrapper');

        if (toggleContent) {
            toggleContent.remove();
        }
        
        let selectedRank = parseInt(new URLSearchParams(window.location.search).get('rank')) || null;

        // 🌟 ESTRELAS
        if (starContainer) {
            const stars = starContainer.querySelectorAll('.star');

            // Aplicar visual ativo se URL já tiver rank
            if (selectedRank) highlightStars(selectedRank);

            stars.forEach(star => {
                star.addEventListener('click', () => {
                    const value = parseInt(star.getAttribute('data-value'));

                    if (selectedRank === value) {
                        selectedRank = null;
                        clearStars();
                    } else {
                        selectedRank = value;
                        highlightStars(value);
                    }

                    loadWithFilters();
                });
            });

            function highlightStars(value) {
                stars.forEach(s => {
                    const v = parseInt(s.getAttribute('data-value'));
                    s.classList.toggle('selected', v <= value);
                });
            }

            function clearStars() {
                stars.forEach(s => s.classList.remove('selected'));
            }
        }

        // 🔍 PESQUISA
        if (searchInput && resultsDiv) {
            let debounce;
            searchInput.addEventListener('input', () => {
                const query = searchInput.value.trim();
                clearTimeout(debounce);
                debounce = setTimeout(() => {
                    if (!query) {
                        resultsDiv.innerHTML = '';
                        return;
                    }

                    fetch(`includes/search-empresas.php?q=${encodeURIComponent(query)}`)
                        .then(res => res.text())
                        .then(html => {
                            resultsDiv.innerHTML = html;
                            document.querySelectorAll('.clickable-product').forEach(item => {
                                item.addEventListener('click', () => {
                                    const id = item.dataset.id;
                                    fetch(`pages/empresas.php?id=${id}&modal=true`)
                                        .then(res => res.text())
                                        .then(modalHtml => {
                                            document.getElementById("modal-container").innerHTML = modalHtml;
                                            document.body.classList.add("no-scroll");
                                            setupGlobalModalListeners();
                                            resultsDiv.innerHTML = '';
                                            searchInput.value = '';
                                        });
                                });
                            });
                        });
                }, 300);
            });

            searchInput.addEventListener('keydown', (e) => {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    loadWithFilters();
                }
            });
        }

        // 🧠 Função geral para construir a query e recarregar
        function loadWithFilters() {
            const search = document.getElementById('search-input')?.value.trim() || '';
            const url = new URLSearchParams();
        
            if (search) url.set('search', search);
            if (selectedRank !== null) url.set('rank', selectedRank);
            if (minViews && maxViews) {
                url.set('min_views', minViews);
                url.set('max_views', maxViews);
            }
        
            url.set('pg', '1');
            loadPage('empresas', url.toString());
        }

    }

    // Clique em cartão → Modal
    document.querySelectorAll('.clickable-card').forEach(card => {
        card.addEventListener('click', () => {
            const id = card.dataset.id;
            fetch(`pages/${page}.php?id=${id}&modal=true`)
                .then(res => res.text())
                .then(html => {
                    document.getElementById("modal-container").innerHTML = html;
                    document.body.classList.add("no-scroll");
                    setupGlobalModalListeners();
                });
        });
    });

    if (page === 'home') initNewsCarousel();

    // Login
    if (page === 'login') {
        const loginForm = document.getElementById('login-form');
        if (loginForm) {
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
    }

    // Registo
    if (page === 'register') {
        const registerForm = document.getElementById('register-form');
        if (registerForm) {
            registerForm.addEventListener('submit', async e => {
                e.preventDefault();
                const formData = new FormData(registerForm);
                const msg = document.getElementById('register-msg');
                msg.innerHTML = 'A processar...';

                try {
                    const res = await fetch('pages/register.php', { method: 'POST', body: formData });
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

    input.addEventListener('input', () => {
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
    });
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


// noticias
function enviarResposta(postId) {
    const resposta = document.getElementById("post_response" + postId).value;

    if (resposta.trim() !== "") {
        const xhr = new XMLHttpRequest();
        xhr.open("POST", "", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4) {
                if (xhr.status === 200) {
                    //alert(resposta);
                    document.getElementById("post_response" + postId).value = "";
                } else {
                    alert("Erro ao enviar resposta.");
                }
            }
        };

        xhr.send("post_id=" + encodeURIComponent(postId) + "&resposta=" + encodeURIComponent(resposta));
    } else {
        alert("Por favor, escreva uma resposta.");
    }
}