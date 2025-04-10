<?php
session_start();

if (!isset($_SESSION['user']) || $_SESSION['user']['user_type'] !== 'COMPANY') {
    echo "<div class='alert alert-danger'>Acesso restrito.</div>";
    exit;
}
?>

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
            <button class="btn custom-btn mt-2" onclick="loadPage('admin/empresa_coms')">Gerir Produtos</button>
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