<?php
include '../includes/db.php';

header('Content-Type: application/json');

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $type_id = $_POST['type_id'] ?? 1;

    if (empty($name) || empty($email) || empty($password)) {
        $response['message'] = 'Preenche todos os campos.';
        echo json_encode($response);
        exit;
    }

    try {
        // Verifica se o utilizador já existe
        $stmt = $pdo->prepare("SELECT USER_ID FROM USER WHERE USER_EMAIL = ?");
        $stmt->execute([$email]);

        if ($stmt->rowCount() > 0) {
            $response['message'] = 'Email já registado.';
            echo json_encode($response);
            exit;
        }

        // Hash da password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Insere novo utilizador
        $stmt = $pdo->prepare("INSERT INTO USER (USER_NAME, USER_EMAIL, USER_PASSWORD, TYPE_ID, USER_STATUS, CREATED_AT) 
                               VALUES (?, ?, ?, ?, 1, NOW())");
        $stmt->execute([$name, $email, $hashedPassword, $type_id]);

        $response['success'] = true;
        $response['message'] = 'Registo realizado com sucesso! Redirecionando...';
    } catch (PDOException $e) {
        $response['message'] = 'Erro: ' . $e->getMessage();
    }
} else {
    $response['message'] = 'Método inválido.';
}

echo json_encode($response);
