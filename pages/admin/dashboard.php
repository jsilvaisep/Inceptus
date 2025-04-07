<?php
session_start();

if (!isset($_SESSION['user']) || $_SESSION['user']['user_type'] !== 'ADMIN') {
    echo "<div class='alert alert-danger'>Acesso restrito.</div>";
    exit;
}
?>

<link rel="stylesheet" href="assets/css/admin.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<br><br>

<div class="container py-5">
    <div class="stats-grid">
        <div class="stat-card">
            <h3 id="total-users">0</h3>
            <p>Utilizadores</p>
        </div>
        <div class="stat-card">
            <h3 id="total-companies">0</h3>
            <p>Empresas</p>
        </div>
        <div class="stat-card">
            <h3 id="total-products">0</h3>
            <p>Produtos</p>
        </div>
    </div>


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

<script src="/assets/js/admin/dashboard.js"></script>
