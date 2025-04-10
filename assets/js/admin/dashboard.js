window.loadDashboard = function () {
    fetch('includes/admin/dashboard.php')
        .then(res => res.json())
        .then(data => {
            if (data.error) {
                document.getElementById('content').innerHTML =
                    '<div class="alert alert-danger">' + data.error + '</div>';
                return;
            }

            const totalUsersEl = document.getElementById('total-users');
            const totalCompaniesEl = document.getElementById('total-companies');
            const totalProductsEl = document.getElementById('total-products');
            const chartEl = document.getElementById('usersChart');

            if (totalUsersEl) totalUsersEl.textContent = data.total_users;
            if (totalCompaniesEl) totalCompaniesEl.textContent = data.total_companies;
            if (totalProductsEl) totalProductsEl.textContent = data.total_products;

            if (chartEl) {
                const ctx = chartEl.getContext('2d');
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: data.chart_labels,
                        datasets: [{
                            label: 'Produtos por Categoria',
                            data: data.chart_values,
                            backgroundColor: 'rgba(44, 83, 100, 0.7)',
                            borderColor: 'rgba(44, 83, 100, 1)',
                            borderWidth: 1,
                            borderRadius: 8
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: { display: false }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: { precision: 0 }
                            }
                        }
                    }
                });
            }
        })
        .catch(err => {
            console.error('Erro ao carregar dashboard:', err);
        });
}
