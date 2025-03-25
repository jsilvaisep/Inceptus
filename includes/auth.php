<?php
session_start();
include 'db.php';

$action = $_POST['action'] ?? '';

if ($action === 'login') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM USER WHERE USER_EMAIL = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['USER_PASSWORD'])) {
        $_SESSION['user'] = [
            'id' => $user['USER_ID'],
            'name' => $user['USER_NAME'],
            'email' => $user['USER_EMAIL'],
            'type' => $user['TYPE_ID']
        ];
        header('Location: ../index.php?page=home');
    } else {
        $_SESSION['error'] = "Credenciais inválidas.";
        header('Location: ../index.php?page=login');
    }
    exit;
}

if ($action === 'register') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $type_id = $_POST['type_id'];

    try {
        $stmt = $pdo->prepare("INSERT INTO USER (USER_NAME, USER_EMAIL, USER_PASSWORD, TYPE_ID) VALUES (?, ?, ?, ?)");
        $stmt->execute([$name, $email, $password, $type_id]);
        $_SESSION['user'] = ['name' => $name, 'email' => $email, 'type' => $type_id];
        header('Location: ../index.php?page=home');
    } catch (PDOException $e) {
        $_SESSION['error'] = "Erro ao registar: " . $e->getMessage();
        header('Location: ../index.php?page=register');
    }
    exit;
}
