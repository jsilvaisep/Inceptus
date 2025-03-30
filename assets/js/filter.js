// Atualiza os produtos com base no filtro de estrelas
document.querySelectorAll('.star').forEach(star => {
    star.addEventListener('click', function () {
        document.querySelectorAll('.star').forEach(s => s.classList.remove('active'));
        this.classList.add('active');

        const selectedRank = this.getAttribute('data-value');

        const search = document.getElementById('search-input')?.value || '';
        const selectedType = document.querySelector('#projectToggle input[name="type"]:checked')?.value || 'both';

        const urlParams = new URLSearchParams();
        if (search) urlParams.set('search', search);
        urlParams.set('pg', '1');
        urlParams.set('type', selectedType);
        urlParams.set('rank', selectedRank);

        loadPage('produtos', urlParams.toString());
    });
});

document.querySelectorAll('#projectToggle input[name="type"]').forEach(input => {
    input.addEventListener('change', function () {
        const selectedType = this.value;

        const search = document.getElementById('search-input')?.value || '';
        const urlParams = new URLSearchParams();

        if (search) urlParams.set('search', search);
        urlParams.set('pg', '1'); // volta sempre à primeira página
        urlParams.set('type', selectedType);

        loadPage('produtos', urlParams.toString());
    });
});
