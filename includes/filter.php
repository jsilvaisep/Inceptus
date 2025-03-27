<link rel="stylesheet" href="assets/css/filter.css">
<div class="filter-container">
    <h2>FILTROS</h2>
    <div class="filter-section">
        <h3>Avaliação</h3>
        <div class="stars">
            <span class="star" onclick="stars()" data-value="1">★</span>
            <span class="star" data-value="2">★</span>
            <span class="star" data-value="3">★</span>
            <span class="star" data-value="4">★</span>
            <span class="star" data-value="5">★</span>
        </div>
    </div>
</div>

<script>

    function stars(){
        alert("Entrou");
    }
    document.querySelectorAll('.star').forEach(star => {
        star.addEventListener('click', function() {
            let value = this.getAttribute('data-value');
            alert('Filtering by'+ value + 'stars');
            // Implement AJAX or page reload filtering logic here
        });
    });
</script>