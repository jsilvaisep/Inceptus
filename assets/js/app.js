// ==========================
// Renderiza as páginas sem ter que recarregar todo o código. 
// Garante que o que temos é um SPA.
// o fetch faz o carregamento da página.
// O setupPageScripts vai buscar o que é suposto apresentar.

(function () {
    let currentPage = null;

    window.navigateTo = function (page, search = '') {
        if (page === currentPage) return;
        currentPage = page;
        loadPage(page, search);
    };

    // ==========================
    function loadPage(page, search = '') {
        let url = 'pages/' + page + '.php';
        if (search) url += '?' + search;

        fetch(url)
            .then(async response => {
                const text = await response.text();
                if (!response.ok) {
                    if (response.status === 403) {
                        throw new Error('forbidden');
                    }
                    throw new Error('notfound');
                }
                return text;
            })
            .then(html => {
                document.getElementById('content').innerHTML = html;
                history.replaceState(null, '', '?page=' + page + (search ? '&' + search : ''));
                setupPageScripts(page);
                updateActiveNavLink(page);

                // Controla visibilidade do carousel
                const carouselContainer = document.getElementById('footer-carousel-container');
                if (carouselContainer) {
                    if (page === 'home') {
                        carouselContainer.style.display = 'block';
                    } else {
                        carouselContainer.style.display = 'none';
                    }
                }
            })
            .catch(err => {
                if (err.message === 'forbidden') {
                    document.getElementById('content').innerHTML = '<h3>⚠️ Acesso negado. Por favor inicie sessão.</h3>';
                } else {
                    document.getElementById('content').innerHTML = '<h3>❌ Página não encontrada.</h3>';
                }
            });
    }

    window.loadPage = loadPage;

    function abrirNoticia(postId) {
        fetch(`pages/noticias.php?id=${postId}&modal=true`)
            .then(res => res.text())
            .then(html => {
                const modal = document.getElementById("modal-container");
                modal.innerHTML = html;
                modal.style.display = "block"; // <- essencial!
                document.body.classList.add("no-scroll");
                setupGlobalModalListeners(); // se tiveres coisas como ESC ou fechar
            });
    }

    // ==========================
// Exemplo de SPA + Funções de criar/editar notícia
// ==========================

// Função para abrir modal e criar nova notícia
    function criarNoticia() {
        document.getElementById('formAction').value = 'criar'; // hidden input no form
        document.getElementById("modalOverlay").style.display = "flex";

        document.getElementById("closeModal").addEventListener("click", function () {
            document.getElementById("modalOverlay").style.display = "none";
        });
    }

// Função para abrir modal para editar notícia
    function editarNoticia(button) {
        document.getElementById('formAction').value = 'editar';
        document.getElementById("modalOverlay").style.display = "flex";

        let row = button.closest('tr');
        let postIdInput = row.querySelector('input[name="editarId"]');
        let postId = postIdInput ? postIdInput.value : null;

        // Poderá guardar esse ID em variável global ou num campo hidden do form
        document.getElementById('post_id_editar').value = postId;

        document.getElementById("closeModal").addEventListener("click", function () {
            document.getElementById("modalOverlay").style.display = "none";
        });
    }

// Submissão do formulário de criar/editar notícias
    document.addEventListener("submit", function (event) {
        if (event.target.id === 'newsForm') {
            event.preventDefault();

            const form = event.target;
            const formData = new FormData(form);

            fetch('criarNoticias.php', {
                method: 'POST',
                body: formData
            })
                .then(async response => {
                    const data = await response.json();
                    if (data.success) {
                        alert(data.success);
                        // Fechar modal e recarregar a página de notícias
                        document.getElementById("modalOverlay").style.display = "none";
                        navigateTo('noticiasdash');
                    } else if (data.error) {
                        alert(data.error);
                    }
                })
                .catch(err => {
                    console.error("Erro na submissão do formulário", err);
                });
        }
    });

// Expor as funções no objeto window, se necessário
    window.criarNoticia = criarNoticia;
    window.editarNoticia = editarNoticia;

    function eliminarProduto(id) {
        alert(id);
    }

    window.eliminarProduto = eliminarProduto;

    //Abre o form após clicar no botão de Criar Produto
    function criarProduto() {
        document.getElementById('formAction').value = 'criar';
        document.getElementById("modalOverlay").style.display = "flex";

        document.getElementById("closeModal").addEventListener("click", function () {
            document.getElementById("modalOverlay").style.display = "none";
        });

    }

    function editarProduto(button) {
        document.getElementById('formAction').value = 'editar';
        document.getElementById("modalOverlay").style.display = "flex";
        let row = button.closest('tr');
        // Find the hidden input inside that row
        let productIdInput = row.querySelector('input[name="editarId"]');
        // Get the value
        let productId = productIdInput ? productIdInput.value : null;
        // Store the ID globally, or pass it into the edit logic
        window.editingProductId = productId;
        document.getElementById("closeModal").addEventListener("click", function () {
            document.getElementById("modalOverlay").style.display = "none";
        });
    }

    window.criarProduto = criarProduto;
    window.editarProduto = editarProduto;

    //Recebe o form e verifica os campos ao criar Produto
    document.addEventListener("submit", function (event) {
        if (event.target.classList.contains('deleteForm')) {
            const form = event.target; // The form that was submitted
            const formData = new FormData(form);

            fetch('/pages/admin/produtosdash.php', {
                method: 'POST',
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    console.log("Success:", data);
                })
                .catch(error => {
                    console.error("Error:", error);
                });
        } else if (event.target.classList.contains('criarProdutoForm')) {
            if (document.getElementById('formAction').value == "editar") {
                let productId = window.editingProductId;
                form = document.getElementById("productForm");
                let formData = new FormData(form);
                formData.append('product-id-editar', productId); // append product ID to form data
                fetch('/includes/criarProdutos.php', {
                    method: 'POST',
                    body: formData
                })
                    .then(response => response.json())
                    .then(data => {
                        console.log("Edit Success:", data);
                    })
                    .catch(error => {
                        console.error("Edit Error:", error);
                    });

            } else {
                form = document.getElementById("productForm");
                let formData = new FormData(form);
                fetch('/includes/criarProdutos.php', {
                    method: 'POST',
                    body: formData
                })
                    .then(response => response.json()) // Handle response
                    .then(data => {
                        console.log("Success:", data);
                    })
                    .catch(error => {
                        console.error("Error:", error);
                    });
            }
            document.getElementById("product_images").addEventListener("change", function (event) {
                const previewContainer = document.getElementById("preview-container");
                previewContainer.innerHTML = "";
                const files = event.target.files;

                if (files.length < 1 || files.length > 5) {
                    alert("Você deve selecionar no mínimo 1 e no máximo 5 imagens.");
                    event.target.value = "";
                    return;
                }

                for (let i = 0; i < files.length; i++) {
                    const file = files[i];

                    if (!file.type.match("image.*")) {
                        alert("Apenas arquivos de imagem são permitidos.");
                        event.target.value = "";
                        return;
                    }

                    const reader = new FileReader();
                    reader.onload = function (e) {
                        const imgElement = document.createElement("img");
                        imgElement.src = e.target.result;
                        imgElement.classList.add("preview-image");
                        previewContainer.appendChild(imgElement);
                    };
                    reader.readAsDataURL(file);
                }
            });

        }
    });

    // ==========================
    // Dropdown do utilizador
    // ==========================
    function setupUserDropdownHoverFix() {
        const dropdowns = document.querySelectorAll('.user-dropdown');
        dropdowns.forEach(dropdown => {
            let timeout;

            dropdown.addEventListener('mouseenter', () => {
                clearTimeout(timeout);
                dropdown.classList.add('show');
            });

            dropdown.addEventListener('mouseleave', () => {
                timeout = setTimeout(() => {
                    dropdown.classList.remove('show');
                }, 300);
            });
        });
    }


    // ==========================
    // Atualiza a barra de navegação, mantendo a lógica do SPA, sem recarregar a página inteira.
    // ==========================
    function reloadNavbar() {
        fetch('includes/navbar.php')
            .then(res => res.text())
            .then(html => {
                document.querySelector('.main-header').outerHTML = html;
                setupUserDropdownHoverFix();

                const params = new URLSearchParams(window.location.search);
                const currentPage = params.get('page') || 'home';
                updateActiveNavLink(currentPage);
            });
    }

    // ==========================
    // Fecha algum modal que esteja aberto.
    // ==========================
    // function closeModal() {
    //     document.getElementById('modal-container').innerHTML = '';
    // }

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
        const searchInput = document.getElementById('search-input');
        const resultsDiv = document.getElementById('search-results');
        const starContainer = document.getElementById('stars');
        const starInput = document.getElementById('.filter-section.stars');
        const toggleInputs = document.querySelectorAll('#projectToggle input[name="type"]');
        const tagSection = document.querySelector('.filter-section.tags');
        const tagInput = document.getElementById('tags');
        const minViewsInput = document.getElementById('min-views');
        const maxViewsInput = document.getElementById('max-views');

        //js dashboard admin
        if (page === 'admin/dashboard') {
            const script = document.createElement('script');
            script.src = 'assets/js/admin/dashboard.js';
            script.onload = () => {
                if (typeof loadDashboard === 'function') loadDashboard();
            };
            document.body.appendChild(script);
        }

        //js admin users
        if (page === 'admin/users') {
            const script = document.createElement('script');
            script.src = 'assets/js/admin/users.js';
            script.onload = () => {
                if (typeof loadUsers === 'function') loadUsers();
            };
            document.body.appendChild(script);
        }
        

        //Paginação das noticias
        if (page === 'noticias') {
            document.querySelectorAll('.page-link').forEach(link => {
                link.addEventListener('click', e => {
                    e.preventDefault();
                    const urlParams = new URL(link.href).searchParams;
                    const pageParam = urlParams.get('page') || 'noticias';
                    const searchParam = urlParams.toString();

                    loadPage(pageParam, searchParam);
                });
            });
        }

        // Pesquisa AJAX dinâmica para produtos e empresas
        if (page === 'produtos') {
            const searchFilterInput = document.getElementById('search-filter');
            let minViews = new URLSearchParams(window.location.search).get('min_views');
            let maxViews = new URLSearchParams(window.location.search).get('max_views');
            let searchTerm = new URLSearchParams(window.location.search).get('search') || '';

            // Inicializar campo de pesquisa do filtro com valor da URL
            if (searchFilterInput && searchTerm) {
                searchFilterInput.value = searchTerm;
            }

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

            if (tagSection) {
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

            if (searchFilterInput) {
                let debounce;
                searchFilterInput.addEventListener('input', () => {
                    clearTimeout(debounce);
                    debounce = setTimeout(() => {
                        loadWithFilters();
                    }, 500); // Atraso para evitar múltiplas requisições
                });
            }

            // 🧠 Função geral para construir a query e recarregar
            function loadWithFilters() {
                const type = document.querySelector('#projectToggle input[name="type"]:checked')?.value || 'both';
                const search = document.getElementById('search-filter')?.value.trim() || '';
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

            //TAGS
            if (tagInput) {
                if (window.Tagify) {
                    const originalNumberFormat = Intl.NumberFormat;

                    Intl.NumberFormat = function (locale, options) {
                        if (options && options.style === 'currency' && !options.currency) {
                            options.currency = 'EUR';
                        }
                        return new originalNumberFormat(locale, options);
                    };
                }

                var tagify = new Tagify(tagInput);

                let initialTags = new URLSearchParams(window.location.search).get('tags');
                if (initialTags) {
                    let tagArray = initialTags.split(',').map(t => ({value: t.trim()}));
                    tagify.addTags(tagArray);
                }

                tagify.on('add', onTagChange);
                tagify.on('remove', onTagChange);
            }

            function onTagChange(e) {
                let tagsData = tagify.value;
                let tagStr = tagsData.map(tag => tag.value).join(',');

                tags = tagStr;

                loadWithFilters();
            }

            let tags = new URLSearchParams(window.location.search).get('tags');

            let minViews = new URLSearchParams(window.location.search).get('min_views');
            let maxViews = new URLSearchParams(window.location.search).get('max_views');

            if (minViewsInput && maxViewsInput) {
                if (minViews) minViewsInput.value = minViews;
                if (maxViews) maxViewsInput.value = maxViews;

                minViewsInput.addEventListener('input', triggerViewsFilter);
                maxViewsInput.addEventListener('input', triggerViewsFilter);
            }

            if (tags) {
                tagInput.value = tags;
                tagInput.addEventListener('input', () => {
                    tags = tagInput.value;
                    loadWithFilters();
                });
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

            if (toggleContent) toggleContent.remove();

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
                if (tags) url.set('tags', tags);
                url.set('pg', '1');
                loadPage('empresas', url.toString());
            }
        }

        // Clique em cartão → Modal
        /*        document.querySelectorAll('.clickable-card').forEach(card => {
                    card.addEventListener('click', () => {
                        const id = card.dataset.id;
                        fetch(`pages/${page}.php?id=${id}&modal=true`)
                            .then(res => res.text())
                            .then(html => {
                                // document.getElementById("modal-container").innerHTML = html;
                                // document.body.classList.add("no-scroll");
                                setupGlobalModalListeners();
                            });
                    });
                });*/

        if (page === 'home') initNewsCarousel();

        // Login
        if (page === 'login') {
            const loginForm = document.getElementById('login-form');
            if (loginForm) {
                loginForm.addEventListener('submit', e => {
                    e.preventDefault();
                    const formData = new FormData(loginForm);
                    const msg = document.getElementById('login-msg');
                    fetch('pages/login.php', {method: 'POST', body: formData, credentials: 'include'})
                        .then(res => res.json())
                        .then(data => {
                            msg.innerHTML = `<p class="${data.success ? 'success' : 'error'}">${data.message}</p>`;
                            if (data.success) {
                                reloadNavbar();
                                setTimeout(() => {
                                    loadPage('home');
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
                        const res = await fetch('pages/register.php', {method: 'POST', body: formData});
                        const data = await res.json();
                        msg.innerHTML = `<p class="${data.success ? 'success' : 'error'}">${data.message}</p>`;

                        if (data.success) {
                            setTimeout(() => {
                                reloadNavbar();
                                loadPage('home');
                            }, 2000);
                        }
                    } catch {
                        msg.innerHTML = `<p class="error">Erro no servidor.</p>`;
                    }
                });
            }
        }

        // Profile
        if (page === 'profile') {
            const profileForm = document.getElementById('profile-form');
            if (profileForm) {
                profileForm.addEventListener('submit', async function (e) {
                    e.preventDefault();
                    const formData = new FormData(profileForm);
                    const msg = document.getElementById('profile-msg');
                    msg.textContent = 'A atualizar...';

                    try {
                        const res = await fetch('pages/profile.php', {
                            method: 'POST',
                            body: formData
                        });

                        const data = await res.json();
                        msg.textContent = data.message;
                        msg.className = data.success ? 'success' : 'error';

                        if (data.success) {
                            reloadNavbar(); // Atualiza a navbar com novo nome/imagem
                        }
                    } catch {
                        msg.textContent = 'Erro no servidor.';
                        msg.className = 'error';
                    }
                });

                const fileInput = document.querySelector('input[name="profile_img"]');
                if (fileInput) {
                    fileInput.addEventListener('change', function (event) {
                        const preview = document.getElementById('avatar-preview');
                        if (event.target.files[0]) {
                            preview.src = URL.createObjectURL(event.target.files[0]);
                        }
                    });
                }
            }
        }


        if (page === 'noticias') {
            document.querySelectorAll('.open-modal-btn').forEach(btn => {
                btn.addEventListener('click', function () {
                    const postId = this.getAttribute('data-id');
                    if (postId) {
                        abrirNoticia(postId);
                    }
                });
            });
        }

        if (page === 'warroom') {
            setupWarroomPage();
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

        navigateTo(page, search);
        setupUserDropdownHoverFix();


        document.body.addEventListener('click', e => {
            const link = e.target.closest('a');
            if (link && link.href.includes('?page=')) {
                e.preventDefault();
                const urlParams = new URL(link.href).searchParams;
                const pageParam = urlParams.get('page') || 'home';

                currentPage = null;

                urlParams.delete('page');
                const searchParam = urlParams.toString();
                navigateTo(pageParam, searchParam);
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

        // Controlar visibilidade inicial do carousel
        const carouselContainer = document.getElementById('footer-carousel-container');

        if (carouselContainer) {
            if (page === 'home') {
                carouselContainer.style.display = 'block';
            } else {
                carouselContainer.style.display = 'none';
            }
        }
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

    function redirectToProduct(productId) {
        // Carrega a página do produto específico com o ID fornecido
        const searchParams = `id=${productId}`;
        loadPage('produtocompleto', searchParams);
    }

    window.redirectToProduct = redirectToProduct;

    function redirectToProductsPage() {
        loadPage('produtos');
    }

    window.redirectToProductsPage = redirectToProductsPage;


    function redirectToCompany(productId) {
        // Carrega a página do produto específico com o ID fornecido
        const searchParams = `id=${productId}`;
        loadPage('empresacompleta', searchParams);
    }

    window.redirectToCompany = redirectToCompany;

    function redirectToCompanyPage() {
        loadPage('empresas');
    }

    window.redirectToCompanyPage = redirectToCompanyPage;

    function redirectToLogin() {
        loadPage('login');
    }

    window.redirectToLogin = redirectToLogin;

    function submitComentarioNoticia(postId) {
        const textarea = document.getElementById('post_response' + postId);
        const resposta = textarea.value.trim();

        if (!resposta) return;

        fetch(`pages/noticiacompleta.php?id=${postId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'include', // ← ISTO É ESSENCIAL
            body: new URLSearchParams({
                resposta: resposta
            })
        })
            .then(response => response.text())
            .then(html => {
                document.getElementById('comment-section').innerHTML = html;
                textarea.value = '';
            })
            .catch(error => console.error('Erro ao enviar comentário:', error));
    }

    window.submitComentarioNoticia = submitComentarioNoticia;

    function submitComentarioProduto(product_Id) {
        const textarea = document.getElementById('comment');
        const resposta = textarea.value.trim();
        const rank = document.getElementById('review')?.value;


        if (!resposta || !rank) {
            alert("Preencha o comentário e o rank.");
            return;
        }

        fetch(`pages/produtocompleto.php?id=${product_Id}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'include',
            body: new URLSearchParams({
                resposta: resposta,
                rank: rank
            })
        })
            .then(response => response.text())
            .then(html => {
                textarea.value = '';
                document.getElementById('review').value = '';
            })
            .catch(error => console.error('Erro ao enviar comentário:', error));
    }

    window.submitComentarioProduto = submitComentarioProduto;

    function setupWarroomPage() {
        const categoryFilter = document.getElementById('category-filter'); // Dropdown de categoria
        const productContainers = document.querySelectorAll('.product-select'); // Blocos de produtos
        const productData = JSON.parse(document.getElementById('warroom-data').dataset.products); // Produtos raw
        const comparisonTable = document.getElementById('comparison-table'); // Tabela de comparação
        const comparisonBody = document.getElementById('comparison-body'); // Corpo da tabela de comparação
        const selectedProducts = {}; // Produtos selecionados para cada slot (máx. 3)

        /** Popula os produtos nos blocos de seleção com base na categoria */
        function populateProductContainers(categoryId) {
            // Filtrar produtos pela categoria ou listar todos
            const products = categoryId === 'all'
                ? Object.values(productData).flat()
                : productData[categoryId] || [];

            // Preencher cada container de produtos
            productContainers.forEach(container => {
                container.innerHTML = ''; // Limpa o conteúdo anterior

                products.forEach(product => {
                    const productOption = document.createElement('div');
                    productOption.className = 'product-option';
                    productOption.dataset.id = product.PRODUCT_ID;
                    productOption.textContent = product.PRODUCT_NAME;

                    productOption.addEventListener('click', function () {
                        const slot = container.dataset.slot; // Em qual bloco estamos?
                        const productId = this.dataset.id;

                        // Desmarca produtos previamente selecionados no mesmo bloco
                        container.querySelectorAll('.product-option.selected').forEach(el =>
                            el.classList.remove('selected')
                        );

                        if (selectedProducts[slot] && selectedProducts[slot] === productId) {
                            // Clique novamente no mesmo produto: remover seleção
                            delete selectedProducts[slot];
                        } else {
                            // Marca o produto como selecionado
                            this.classList.add('selected');
                            selectedProducts[slot] = productId;
                        }

                        // Atualiza a tabela de comparação
                        updateComparisonTable(Object.values(selectedProducts).map(id => {
                            return Object.values(productData).flat().find(p => p.PRODUCT_ID == id);
                        }));
                    });

                    container.appendChild(productOption);
                });
            });
        }

        /** Atualiza a tabela de comparação com os dados dos produtos */
        function updateComparisonTable(products) {
            comparisonBody.innerHTML = ''; // Limpa o corpo da tabela anterior

            if (products.length === 0) {
                comparisonTable.style.display = 'none';
                return;
            }
            comparisonTable.style.display = 'table';

            // Atualiza o cabeçalho com os nomes dos produtos selecionados
            const tableHeader = comparisonTable.querySelector('thead tr');
            tableHeader.innerHTML = '';

            const specHeader = document.createElement('th');
            specHeader.textContent = 'Especificações';
            tableHeader.appendChild(specHeader);

            for (let i = 0; i < 3; i++) {
                const headerCell = document.createElement('th');
                headerCell.textContent = products[i] ? products[i].PRODUCT_NAME : 'Vazio';
                tableHeader.appendChild(headerCell);
            }

            // Atributos principais fixos
            const attributes = [
                {label: 'Ranking do Produto', key: 'PRODUCT_RANK'},
                {label: 'Visualizações', key: 'PRODUCT_VIEW_QTY'}
            ];

            attributes.forEach(attribute => {
                const row = document.createElement('tr');

                const specCell = document.createElement('td');
                specCell.textContent = attribute.label;
                specCell.classList.add('spec-column');
                row.appendChild(specCell);

                products.forEach(product => {
                    const valueCell = document.createElement('td');
                    valueCell.textContent = product ? product[attribute.key] ?? '—' : '—';
                    row.appendChild(valueCell);
                });

                for (let i = 0; i < 3 - products.length; i++) {
                    const emptyCell = document.createElement('td');
                    emptyCell.textContent = '—';
                    row.appendChild(emptyCell);
                }

                comparisonBody.appendChild(row);
            });

            // 🔽 Adiciona linhas para cada chave do campo SPECS (JSON interno)
            const specsKeys = new Set();
            const parsedSpecsList = [];

            // Primeiro, parse dos SPECS e coleta das chaves
            products.forEach(product => {
                let specsObj = {};

                if (product && product.SPECS) {
                    if (typeof product.SPECS === 'string') {
                        try {
                            specsObj = JSON.parse(product.SPECS);
                        } catch (e) {
                            specsObj = {};
                        }
                    } else if (typeof product.SPECS === 'object') {
                        specsObj = product.SPECS;
                    }
                }

                parsedSpecsList.push(specsObj);
                Object.keys(specsObj).forEach(key => specsKeys.add(key));
            });

            // Agora, renderiza as linhas das SPECS
            specsKeys.forEach(specKey => {
                const row = document.createElement('tr');

                const specCell = document.createElement('td');
                specCell.textContent = specKey;
                specCell.classList.add('spec-column');
                row.appendChild(specCell);

                parsedSpecsList.forEach(specs => {
                    const valueCell = document.createElement('td');
                    valueCell.textContent = specs[specKey] ?? '—';
                    row.appendChild(valueCell);
                });

                for (let i = 0; i < 3 - parsedSpecsList.length; i++) {
                    const emptyCell = document.createElement('td');
                    emptyCell.textContent = '—';
                    row.appendChild(emptyCell);
                }

                comparisonBody.appendChild(row);
            });

        }

        // Listener do dropdown de categorias
        categoryFilter.addEventListener('change', function () {
            const categoryId = this.value; // Obtém ID da categoria selecionada
            populateProductContainers(categoryId);
        });

        // Inicializa com todos os produtos
        populateProductContainers('all');
    }

    function submitEditarEmpresaAdmin(company_id) {
        alert('Editar empresas dashboard admin ' + company_id);
    }

    window.submitEditarEmpresaAdmin = submitEditarEmpresaAdmin;

    function submitEliminarEmpresaAdmin(company_id) {
        alert('Eliminar empresas dashboard admin ' + company_id);
    }

    window.submitEliminarEmpresaAdmin = submitEliminarEmpresaAdmin;

    function submitEditarUsersAdmin(user_id) {
        alert('Editar users dashboard admin ' + user_id);
    }

    window.submitEditarUsersAdmin = submitEditarUsersAdmin;

    function submitEliminarUsersAdmin(user_id) {
        alert('Eliminar users dashboard admin ' + user_id);
    }

    window.submitEliminarUsersAdmin = submitEliminarUsersAdmin;

    function submitEditarProdutosAdmin(product_id) {
        alert('Editar produtos dashboard admin ' + product_id);
    }

    window.submitEditarProdutosAdmin = submitEditarProdutosAdmin;

    function submitEliminarProdutosAdmin(product_id) {
        alert('Eliminar produtos dashboard admin ' + product_id);
    }

    window.submitEliminarProdutosAdmin = submitEliminarProdutosAdmin;

    /*    function submitEditarNoticiasAdmin(post_id){
            alert('Editar notícia dashboard admin '+post_id);
        }

        window.submitEditarNoticiasAdmin = submitEditarNoticiasAdmin;*/
    function submitEliminarNoticiasAdmin(post_id) {
        const formData = new FormData();
        formData.append('post_id', post_id);
        formData.append('action', 'delete');

        fetch('pages/admin/noticias.php', {
            method: 'POST',
            body: formData
        })
            .then(response => response.text())  // Use response.text() para depurar o conteúdo bruto
            .then(text => {
                console.log('Resposta do servidor:', text);  // Veja o que está sendo retornado
                try {
                    const data = JSON.parse(text);  // Tente converter para JSON
                    if (data.success) {
                        alert(data.message);
                        loadPage('admin/noticias');
                    } else {
                        alert(data.message || 'Erro ao eliminar a notícia.');
                    }
                } catch (e) {
                    console.error('Erro ao processar a resposta JSON:', e);
                    alert('Ocorreu um erro ao processar a resposta.');
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                alert('Ocorreu um erro ao processar a solicitação.');
            });

    }

    window.submitEliminarNoticiasAdmin = submitEliminarNoticiasAdmin;

    function updateActiveNavLink(currentPage) {
        document.querySelectorAll('.navbar ul li a').forEach(link => {
            link.classList.remove('active');
        });

        let rootSection = currentPage;

        const pageMapping = {
            'produtocompleto': 'produtos',
            'empresacompleta': 'empresas',
            'noticiacompleta': 'noticias'
        };

        if (pageMapping[currentPage]) {
            rootSection = pageMapping[currentPage];
        }

        let activeLink = document.querySelector(`.navbar ul li a[href*="?page=${currentPage}"]`);

        if (!activeLink && rootSection !== currentPage) {
            activeLink = document.querySelector(`.navbar ul li a[href*="?page=${rootSection}"]`);
        }

        if (activeLink) {
            activeLink.classList.add('active');
        }
    }
})();