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
    </div>
</div>

<script>
    document.querySelectorAll('.star').forEach(star => {
        star.addEventListener('click', function() {
            let value = this.getAttribute('data-value');
            alert('Filtering by'+ value + 'stars');
            // Implement AJAX or page reload filtering logic here
        });
    });
</script>