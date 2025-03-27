<?php
session_start();

if (!isset($_SESSION['user']) || $_SESSION['user']['user_type'] !== 'ADMIN') {
    echo "<div class='alert alert-danger'>Acesso restrito.</div>";
    exit;
}
?>

<div class="container py-5">
    <h1 class="mb-5 text-center fw-bold">Painel de Administração</h1>

    <div class="admin-grid">
        <div class="custom-card">
            <div class="card-body text-center">
                <i class="fas fa-users-cog fa-2x text-primary mb-2"></i>
                <h6 class="fw-bold">Gestão de Utilizadores</h6>
                <p class="text-muted small">Ver, editar ou eliminar contas.</p>
                <button class="btn custom-btn mt-2" onclick="loadPage('admin/users')">Gerir Utilizadores</button>
            </div>
        </div>

        <div class="custom-card">
            <div class="card-body text-center">
                <i class="fas fa-building fa-2x text-danger mb-2"></i>
                <h6 class="fw-bold">Empresas</h6>
                <p class="text-muted small">Aprovar ou rejeitar empresas.</p>
                <button class="btn custom-btn mt-2" onclick="loadPage('admin/empresas')">Gerir Empresas</button>
            </div>
        </div>

        <div class="custom-card">
            <div class="card-body text-center">
                <i class="fas fa-box-open fa-2x text-success mb-2"></i>
                <h6 class="fw-bold">Produtos</h6>
                <p class="text-muted small">Aprovar ou remover produtos.</p>
                <button class="btn custom-btn mt-2" onclick="loadPage('admin/produtos')">Gerir Produtos</button>
            </div>
        </div>

        <div class="custom-card">
            <div class="card-body text-center">
                <i class="fas fa-database fa-2x text-warning mb-2"></i>
                <h6 class="fw-bold">Backups</h6>
                <p class="text-muted small">Exportar ou restaurar dados.</p>
                <button class="btn custom-btn mt-2" onclick="loadPage('admin/backups')">Gestão de Backups</button>
            </div>
        </div>

        <div class="custom-card">
            <div class="card-body text-center">
                <i class="fas fa-cogs fa-2x text-dark mb-2"></i>
                <h6 class="fw-bold">Definições</h6>
                <p class="text-muted small">Ajustar configurações da plataforma.</p>
                <button class="btn custom-btn mt-2" onclick="loadPage('admin/settings')">Abrir Definições</button>
            </div>
        </div>

        <div class="custom-card">
            <div class="card-body text-center">
                <i class="fas fa-chart-line fa-2x text-info mb-2"></i>
                <h6 class="fw-bold">Estatísticas</h6>
                <p class="text-muted small">Visualizar dados analíticos.</p>
                <button class="btn custom-btn mt-2" onclick="loadPage('admin/stats')">Ver Estatísticas</button>
            </div>
        </div>
    </div>
</div>

<style>
.admin-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 2rem;
    justify-items: center;
}

.custom-card {
    background: #fff;
    border-radius: 20px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    transition: transform 0.2s ease-in-out;
    padding: 1.5rem;
    width: 100%;
    max-width: 300px;
}

.custom-card:hover {
    transform: translateY(-5px);
}

.custom-btn {
    background: linear-gradient(135deg, #0f2027, #203a43, #2c5364);
    color: white;
    border: none;
    border-radius: 50px;
    padding: 8px 18px;
    font-weight: bold;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
    transition: background 0.3s ease-in-out;
    font-size: 0.9rem;
}

.custom-btn:hover {
    background: linear-gradient(135deg, #2c5364, #203a43, #0f2027);
    color: white;
}
</style>