function strills(){
    alert('entrou');
    document.querySelectorAll('.star').forEach(star => {
        star.addEventListener('click', function() {
            let value = this.getAttribute('data-value');
            alert('Filtering by'+ value + 'stars');
            // Implement AJAX or page reload filtering logic here
        });
    });
}