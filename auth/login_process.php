<?php
include '../includes/db.php';
session_start();
header('Content-Type: application/json');

$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

if (!$email || !$password) {
    echo json_encode(['success' => false, 'message' => 'Preencha todos os campos.']);
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT * FROM USER WHERE USER_EMAIL = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['USER_PASSWORD'])) {
        $_SESSION['user_id'] = $user['USER_ID'];
        $_SESSION['user_name'] = $user['USER_NAME'];
        echo json_encode(['success' => true, 'message' => 'Login efetuado com sucesso!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Credenciais inválidas.']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Erro ao validar login.']);
}
?>