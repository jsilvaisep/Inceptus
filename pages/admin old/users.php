<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['user_type'] !== 'ADMIN') {
    echo "<div class='alert alert-danger'>Acesso restrito.</div>";
    exit;
}
?>

<div class="container py-5">
    <h2 class="text-center fw-bold mb-4">Gestão de Utilizadores</h2>
    <p class="text-muted text-center">Página em construção. Em breve, poderá gerir esta secção.</p>
</div>
