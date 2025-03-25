<?php
include '../includes/db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (!empty($email) && !empty($password)) {
        $stmt = $pdo->prepare("SELECT * FROM USER WHERE USER_EMAIL = :email");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['USER_PASSWORD'])) {
            $_SESSION['user_id'] = $user['USER_ID'];
            $_SESSION['user_name'] = $user['USER_NAME'];
            header('Location: ../index.php?page=home');
            exit;
        } else {
            echo "Email ou palavra-passe incorretos.";
        }
    } else {
        echo "Preencha todos os campos.";
    }
}
?>
