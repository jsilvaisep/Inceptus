window.loadEstatisticas = function () {
    fetch("pages/admin/estatisticas.php?fetch=1")
        .then(res => res.json())
        .then(data => {
            renderCards(data.totals);
            renderGraficoLinhas(data.porMes);
            renderDoughnuts(data.ativos);
            renderTabelas(data.topEmpresas, data.topProdutos, data.ultimasNoticias);
        })
        .catch(err => {
            console.error("Erro ao carregar estatísticas:", err);
        });
};

function renderCards(totals) {
    const wrapper = document.getElementById("stats-cards");
    wrapper.innerHTML = `
        <div class="stat-card"><h3>${totals.users}</h3><p>Utilizadores</p></div>
        <div class="stat-card"><h3>${totals.empresas}</h3><p>Empresas</p></div>
        <div class="stat-card"><h3>${totals.produtos}</h3><p>Produtos</p></div>
        <div class="stat-card"><h3>${totals.noticias}</h3><p>Notícias</p></div>
    `;
}

function renderGraficoLinhas(porMes) {
    const ctx = document.getElementById("grafico-linhas").getContext("2d");

    const labels = [...new Set([
        ...porMes.users.map(x => x.mes),
        ...porMes.empresas.map(x => x.mes),
        ...porMes.produtos.map(x => x.mes)
    ])];

    const mapToChartData = (data) => {
        return labels.map(label => {
            const found = data.find(d => d.mes === label);
            return found ? found.total : 0;
        });
    };

    new Chart(ctx, {
        type: 'line',
        data: {
            labels,
            datasets: [
                {
                    label: 'Utilizadores',
                    data: mapToChartData(porMes.users),
                    borderWidth: 2
                },
                {
                    label: 'Empresas',
                    data: mapToChartData(porMes.empresas),
                    borderWidth: 2
                },
                {
                    label: 'Produtos',
                    data: mapToChartData(porMes.produtos),
                    borderWidth: 2
                }
            ]
        },
        options: {
            responsive: true,
            tension: 0.3
        }
    });
}

function renderDoughnuts(ativos) {
    const configs = [
        { id: "doughnut-users", label: "Utilizadores", data: ativos.users },
        { id: "doughnut-empresas", label: "Empresas", data: ativos.empresas },
        { id: "doughnut-produtos", label: "Produtos", data: ativos.produtos },
    ];

    configs.forEach(cfg => {
        new Chart(document.getElementById(cfg.id), {
            type: 'doughnut',
            data: {
                labels: ["Ativos", "Inativos"],
                datasets: [{
                    data: [cfg.data.ativos, cfg.data.inativos],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    title: {
                        display: true,
                        text: cfg.label
                    }
                }
            }
        });
    });
}

function renderTabelas(empresas, produtos, noticias) {
    const renderTable = (title, rows, headers) => {
        return `
            <div class="ranking-table">
                <h4>${title}</h4>
                <table>
                    <thead>
                        <tr>${headers.map(h => `<th>${h}</th>`).join("")}</tr>
                    </thead>
                    <tbody>
                        ${rows.map(row => `
                            <tr>
                                ${Object.values(row).map(col => `<td>${col}</td>`).join("")}
                            </tr>
                        `).join("")}
                    </tbody>
                </table>
            </div>
        `;
    };

    document.getElementById("top-empresas").innerHTML = renderTable(
        "Top Empresas com mais Produtos",
        empresas,
        ["Empresa", "Total"]
    );

    document.getElementById("top-produtos").innerHTML = renderTable(
        "Top Produtos por Rank",
        produtos.map(p => ({
            "Produto": p.PRODUCT_NAME,
            "Rank": p.PRODUCT_RANK,
            "Empresa": p.COMPANY_NAME
        })),
        ["Produto", "Rank", "Empresa"]
    );

    document.getElementById("ultimas-noticias").innerHTML = renderTable(
        "Últimas Notícias",
        noticias.map(n => ({
            "Notícia": n.POST_CONTENT,
            "Empresa": n.COMPANY_NAME,
            "Estado": n.POST_STATUS
        })),
        ["Notícia", "Empresa", "Estado"]
    );
}

document.addEventListener("DOMContentLoaded", loadEstatisticas);
