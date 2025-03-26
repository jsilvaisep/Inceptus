<?php
session_start();

function isLoggedIn() {
    return isset($_SESSION['user']);
}

if (!isset($_SESSION['user_id'])) {
    header("Location: /auth/login.php");
    exit();
}
