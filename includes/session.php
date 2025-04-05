<?php
session_save_path(__DIR__ . '/sessions');

if (!file_exists(session_save_path())) {
    mkdir(session_save_path(), 0777, true);
}

session_start();

/**
 * Verifica se o utilizador está autenticado
 */
function isLoggedIn() {
    return isset($_SESSION['user']) && !empty($_SESSION['user']['user_id']);
}

/**
 * Verifica se o utilizador está desautenticado
 */
function isLoggedOut() {
    return !isLoggedIn();
}

/**
 * Se necessário forçar autenticação, utiliza esta função.
 * Ideal para páginas não carregadas por SPA.
 */
function requireLogin() {
    if (isLoggedOut()) {
        // Redirecionar para login tradicional (ex: caso aceda direto a profile.php sem SPA)
        header("Location: /?page=login");
        exit();
    }
}
