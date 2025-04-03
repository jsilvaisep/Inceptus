<?php include '../includes/db.php';
session_start();
if (isset($_SESSION['user'])) {
    $userID = $_SESSION['user']['user_id'];
    $userName = $_SESSION['user']['user_name'] ?? '';
} else {
    header("Location: /pages/login.php");
    exit;
}

?>
<h2>Warroom</h2><p>Ambiente estratégico da plataforma.</p>