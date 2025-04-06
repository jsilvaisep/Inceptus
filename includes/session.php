<?php
session_save_path(__DIR__ . '/sessions');

if (!file_exists(session_save_path())) {
    mkdir(session_save_path(), 0777, true);
}

session_start();
function isLoggedIn() {
    return isset($_SESSION['user']) && !empty($_SESSION['user']['user_id']);
}
function isLoggedOut() {
    return !isLoggedIn();
}
function requireLogin() {
    if (isLoggedOut()) {
        header("Location: /?page=login");
        exit();
    }
}
