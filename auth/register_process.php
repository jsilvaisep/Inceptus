<?php
include '../includes/db.php';
header('Content-Type: application/json');

$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$type_id = $_POST['type_id'] ?? 1;

if (!$name || !$email || !$password) {
    echo json_encode(['success' => false, 'message' => 'Preencha todos os campos.']);
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT USER_ID FROM USER WHERE USER_EMAIL = ?");
    $stmt->execute([$email]);

    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => false, 'message' => 'Email já registado.']);
        exit;
    }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("INSERT INTO USER (USER_NAME, USER_EMAIL, USER_PASSWORD, TYPE_ID, USER_STATUS, CREATED_AT) 
                           VALUES (?, ?, ?, ?, 1, NOW())");
    $stmt->execute([$name, $email, $hashedPassword, $type_id]);

    echo json_encode(['success' => true, 'message' => 'Registo realizado com sucesso!']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Erro no servidor.']);
}
?>