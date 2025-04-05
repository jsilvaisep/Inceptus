// ==========================
// Renderiza as páginas sem ter que recarregar todo o código. 
// Garante que o que temos é um SPA.
// o fetch faz o carregamento da página.
// O setupPageScripts vai buscar o que é suposto apresentar.

(function() {
    let currentPage = null;

    window.navigateTo = function(page, search = '') {
        if (page === currentPage) return;
        currentPage = page;
        loadPage(page, search);
    };

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


    //Abre o form após clicar no botão de Criar Produto
    function criarProduto(){
            document.getElementById("modalOverlay").style.display = "flex";

        document.getElementById("closeModal").addEventListener("click", function() {
            document.getElementById("modalOverlay").style.display = "none";
        });

    }

    //Recebe o form e verifica os campos ao criar Produto
    document.addEventListener("submit", function (event) {
        form = document.getElementById("productForm");
        let formData = new FormData(form);

        fetch('/includes/criarProdutos.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.text()) // Handle response
        .then(data => {
            console.log("Success:", data);
        })
        .catch(error => {
            console.error("Error:", error);
        });
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
            let name = document.getElementById("product_name").value.trim();
            let description = document.getElementById("product_description").value.trim();
            let category = document.getElementById("category_id").value;
            let company = "4ce516e6-0be9-11f0-b0d3-020017000d59";

            if (!name || !description || !category || !company) {
                alert("Todos os campos são obrigatórios.");
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

        // Pesquisa AJAX dinâmica para produtos e empresas
        if (page === 'produtos') {
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

            //TAGS
            if (tagInput) {
                if (window.Tagify) {
                    const originalNumberFormat = Intl.NumberFormat;
                    
                    Intl.NumberFormat = function(locale, options) {
                        if (options && options.style === 'currency' && !options.currency) {
                            options.currency = 'EUR'; 
                        }
                        return new originalNumberFormat(locale, options);
                    };
                }
                
                var tagify = new Tagify(tagInput);

                let initialTags = new URLSearchParams(window.location.search).get('tags');
                if (initialTags) {
                    let tagArray = initialTags.split(',').map(t => ({ value: t.trim() }));
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

            if (toggleContent)    toggleContent.remove();

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
                if(tags) url.set('tags', tags);
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
                        const res = await fetch('pages/register.php', { method: 'POST', body: formData });
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
    function enviarRespostaNoticia(postId) {
        const textarea = document.getElementById("post_response" + postId);
        const resposta = textarea.value.trim();
        if (!resposta) {
            alert("Por favor, escreva um comentário.");
            return false;
        }
    
        fetch(`pages/noticiacompleta.php?id=${postId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: `resposta=${encodeURIComponent(resposta)}`
        })
        .then(res => res.text())
        .then(html => {
            document.getElementById("comment-section").innerHTML = html;
            textarea.value = "";
        })
        .catch(() => {
            alert("Erro ao submeter o comentário.");
        });
    
        return false;
    }
    

    function redirectToProduct(productId) {
        // Carrega a página do produto específico com o ID fornecido
        const searchParams = `id=${productId}`;
        loadPage('produtocompleto', searchParams);
    }
    window.redirectToProduct = redirectToProduct;

    function redirectToCompany(productId) {
        // Carrega a página do produto específico com o ID fornecido
        const searchParams = `id=${productId}`;
        loadPage('empresacompleta', searchParams);
    }
    window.redirectToCompany = redirectToCompany;

    function redirectToLogin() {
        loadPage('login');
    }
    window.redirectToLogin = redirectToLogin;

})();