<?php 
$type = $_GET['type'] ?? 'both'; // valor atual
$rank = isset($_GET['rank']) ? (int) $_GET['rank'] : 0;
$minViews = isset($_GET['min_views']) ? (int) $_GET['min_views'] : '';
$maxViews = isset($_GET['max_views']) ? (int) $_GET['max_views'] : '';
$tags = isset($_GET['tags']) ? explode(',', $_GET['tags']) : []; // array de strings
$search = $_GET['search'] ?? '';
?>

<div class="filter-container">
    <h2>FILTROS</h2>

    <!-- Campo de Pesquisa -->
    <div class="filter-section">
        <h3>Pesquisa</h3>
        <div class="search-box">
            <input type="text" id="search-filter" class="search-input" placeholder="Pesquisar..." value="<?= htmlspecialchars($search) ?>">
        </div>
    </div>

    <!-- Avaliação -->
    <div class="filter-section">
        <h3>Avaliação</h3>
        <div id="stars" class="stars">
            <?php for ($i = 1; $i <= 5; $i++): ?>
                <span class="star <?= $rank === $i ? 'selected' : '' ?>" data-value="<?= $i ?>">★</span>
            <?php endfor; ?>
        </div>
    </div>

    <!-- Produtos / Projetos -->
    <div class="filter-section custom-toggle-wrapper">
        <h3>Produtos / Projeto</h3>
        <div class="custom-toggle" id="projectToggle">
            <input type="radio" name="type" value="products" id="toggle-products" <?= $type === 'products' ? 'checked' : '' ?>>
            <label for="toggle-products" title="Produtos">📦</label>

            <input type="radio" name="type" value="both" id="toggle-both" <?= $type === 'both' ? 'checked' : '' ?>>
            <label for="toggle-both" title="Ambos">🔁</label>

            <input type="radio" name="type" value="projects" id="toggle-projects" <?= $type === 'projects' ? 'checked' : '' ?>>
            <label for="toggle-projects" title="Projetos">🛠</label>

            <div class="toggle-slider"></div>
        </div>
    </div>

    
    <!-- Views -->
    <div class="filter-section views-filter">
        <h3>Views:</h3>
        <div class="views-range">
            <label for="min-views">Min:</label>
            <input type="number" id="min-views" name="min_views" placeholder="100">

            <label for="max-views">Max:</label>
            <input type="number" id="max-views" name="max_views" placeholder="500">
        </div>
    </div>

    
    <!-- Tags -->
    <div class="filter-section tags">
        <h3>Tags</h3>
        <div class="tag-filter">
            <input type="text" id="tags" name="tags" placeholder="Escreve para procurar tags..." />
        </div>
    </div>
</div>