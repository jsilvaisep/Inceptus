<?php
session_save_path(__DIR__ . '/../sessions');
if (!file_exists(session_save_path())) {
    mkdir(session_save_path(), 0777, true);
}

session_start();

function isLoggedIn()
{
    return isset($_SESSION['user']);
}
function isLoggedOut()
{
    return !isLoggedIn();
}

function requireLogin()
{
    if (!isLoggedIn()) {
        // Verifica se é chamada AJAX (SPA) → mostra erro 403
        if (
            isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
            $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest'
        ) {
            http_response_code(403);
            echo "⚠️ Acesso negado. Por favor inicie sessão.";
        } else {
            // Se for acesso direto, redireciona para login
            header("Location: /pages/login.php");
        }
        exit;
    }
}