<link rel="stylesheet" href="assets/css/filter.css">
<div class="filter-container">
    <h2>FILTROS</h2>
    <div class="filter-section">
        <h3>Avaliação</h3>
        <div class="stars">
            <span class="star" onclick="stars(1)" data-value="1">★</span>
            <span class="star" onclick="stars(2)" data-value="2">★</span>
            <span class="star" onclick="stars(3)" data-value="3">★</span>
            <span class="star" onclick="stars(4)" data-value="4">★</span>
            <span class="star" onclick="stars(5)" data-value="5">★</span>
        </div>
        <br />
        <h3>Produtos / Projeto</h3>
        <div class="toggle-switch">
            <!-- Opção 1: Produtos -->
            <input type="radio" name="viewMode" value="products" id="toggle-products" checked>
            <label for="toggle-products" class="toggle-label">Produtos</label>

            <!-- Opção 2: Ambos -->
            <input type="radio" name="viewMode" value="both" id="toggle-both">
            <label for="toggle-both" class="toggle-label">Ambos</label>

            <!-- Opção 3: Projetos -->
            <input type="radio" name="viewMode" value="projects" id="toggle-projects">
            <label for="toggle-projects" class="toggle-label">Projetos</label>
        </div>
    </div>
</div>

<script>
    document.querySelectorAll('.star').forEach(star => {
        star.addEventListener('click', function () {
            const value = this.getAttribute('data-value');
            const params = new URLSearchParams(window.location.search);
            params.set('rank', value);
            loadPage('produtos', params.toString());
        });
    });
</script>
