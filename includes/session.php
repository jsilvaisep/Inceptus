<?php
session_save_path(__DIR__ . '/sessions');

if (!file_exists(session_save_path())) {
    mkdir(session_save_path(), 0777, true);
}

session_start();

function isLoggedIn() {
    return isset($_SESSION['user']);
}

if (!isset($_SESSION['user_id'])) {
    header("Location: /auth/login.php");
    exit();
}
