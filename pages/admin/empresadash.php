<?php
session_start();

if (!isset($_SESSION['user']) || $_SESSION['user']['user_type'] !== 'COMPANY') {
    echo "<div class='alert alert-danger'>Acesso restrito.</div>";
    exit;
}
?>
<link rel="stylesheet" href="assets/css/admin.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="container py-5">
    <h1 class="mb-5 text-center fw-bold">Painel de Administração</h1>
</div>
<div class="admin-grid">
    <div class="custom-card">
        <div class="card-body text-center">
            <i class="fas fa-users-cog fa-2x text-primary mb-2"></i>
            <h3 class="fw-bold">Produtos</h3>
            <p class="text-muted small">Aprovar ou remover produtos.</p>
            <button class="btn custom-btn mt-2" onclick="loadPage('admin/produtosdash')">Gerir Produtos</button>
        </div>
    </div>
    <div class="custom-card">
        <div class="card-body text-center">
            <i class="fas fa-building fa-2x text-danger mb-2"></i>
            <h3 class="fw-bold">Comentários</h3>
            <p class="text-muted small">Listar comentários produtos.</p>
            <button class="btn custom-btn mt-2" onclick="loadPage('admin/empresa_coms')">Gerir Comentários</button>
        </div>
    </div>
    <div class="custom-card">
        <div class="card-body text-center">
            <i class="fas fa-newspaper fa-2x text-primary mb-2"></i>
            <h3 class="fw-bold">Notícias</h3>
            <p class="text-muted small">Listar e gerir notícias do site.</p>
            <button class="btn custom-btn mt-2" onclick="loadPage('admin/noticiasdash')">Gerir Notícias</button>
        </div>
    </div>