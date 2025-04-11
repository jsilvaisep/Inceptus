window.loadEstatisticas = function () {
    const cardsContainer = document.getElementById("stats-cards");
    const graficoCanvas = document.getElementById("grafico-usuarios");

    cardsContainer.innerHTML = "A carregar estatísticas...";

    fetch("pages/admin/estatisticas.php?fetch=1")
        .then(res => res.json())
        .then(data => {
            // Render cards
            const totals = data.totals;
            const cards = `
                <div class="stat-card"><h3>${totals.users}</h3><p>Utilizadores</p></div>
                <div class="stat-card"><h3>${totals.empresas}</h3><p>Empresas</p></div>
                <div class="stat-card"><h3>${totals.produtos}</h3><p>Produtos</p></div>
                <div class="stat-card"><h3>${totals.noticias}</h3><p>Notícias</p></div>
            `;
            cardsContainer.innerHTML = `<div class="stat-card-wrapper">${cards}</div>`;

            // Gráfico - Registo de utilizadores por mês
            const labels = data.grafico_usuarios.map(item => item.mes);
            const valores = data.grafico_usuarios.map(item => item.total);

            new Chart(graficoCanvas, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Utilizadores criados por mês',
                        data: valores,
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            stepSize: 1
                        }
                    }
                }
            });
        })
        .catch(err => {
            cardsContainer.innerHTML = "<p>Erro ao carregar estatísticas.</p>";
            console.error(err);
        });
};

document.addEventListener("DOMContentLoaded", loadEstatisticas);
