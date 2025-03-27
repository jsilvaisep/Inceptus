function stars(value){
    <alert("Entrou");
    document.querySelectorAll('.star').forEach(star => {
        star.addEventListener('click', function() {
            let value = this.getAttribute('data-value');
            alert(value);
        });
    });>
}
