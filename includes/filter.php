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
        <div class="custom-toggle-wrapper">
            <h3>Produtos / Projeto</h3>
            <div class="custom-toggle" id="projectToggle">
                <input type="radio" name="type" value="products" id="toggle-products" checked>
                <label for="toggle-products" title="Produtos">📦</label>

                <input type="radio" name="type" value="both" id="toggle-both">
                <label for="toggle-both" title="Ambos">🔁</label>

                <input type="radio" name="type" value="projects" id="toggle-projects">
                <label for="toggle-projects" title="Projetos">🛠</label>

                <div class="toggle-slider"></div>
            </div>
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

<style>
    .custom-toggle-wrapper {
        margin-top: 10px;
        color: white;
        font-weight: bold;
    }

    .custom-toggle {
        display: flex;
        position: relative;
        width: 180px;
        height: 40px;
        background: #1f1f1f;
        border-radius: 999px;
        overflow: hidden;
        align-items: center;
        justify-content: space-between;
        padding: 0 5px;
        gap: 3px;
    }

    .custom-toggle input {
        display: none;
    }

    .custom-toggle label {
        flex: 1;
        height: 100%;
        line-height: 40px;
        text-align: center;
        cursor: pointer;
        z-index: 2;
        color: white;
        font-size: 18px;
        transition: color 0.3s ease;
    }

    .toggle-slider {
        position: absolute;
        top: 3px;
        left: 3px;
        width: calc(33.333% - 6px);
        height: calc(100% - 6px);
        background: #f2f2f2;
        border-radius: 50px;
        z-index: 1;
        transition: left 0.3s ease;
        box-shadow: 0 0 5px rgba(0,0,0,0.3);
    }

    /* Movimentar o slider */
    #toggle-both:checked ~ .toggle-slider {
        left: calc(33.333% + 3px);
    }

    #toggle-projects:checked ~ .toggle-slider {
        left: calc(66.666% + 3px);
    }
</style>