<?php 
$type = $_GET['type'] ?? 'both'; // valor atual
$rank = isset($_GET['rank']) ? (int) $_GET['rank'] : 0;
?>

<div class="filter-container">
    <h2>FILTROS</h2>

    <!-- Estrelas -->
    <div class="filter-section">
        <h3>Avaliação</h3>
        <div id="stars" class="stars">
            <?php for ($i = 1; $i <= 5; $i++): ?>
                <span class="star <?= $rank === $i ? 'selected' : '' ?>" data-value="<?= $i ?>">★</span>
            <?php endfor; ?>
        </div>
    </div>

    <!-- Toggle produtos/projetos -->
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
</div>
