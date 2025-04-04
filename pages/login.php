<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    include '../includes/db.php';
    header('Content-Type: application/json');

    $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
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
            $stmtType = $pdo->prepare("SELECT ut.USER_TYPE, ut.TYPE_ID 
                                       FROM USER_TYPE ut 
                                       WHERE ut.TYPE_ID = ?");
            $stmtType->execute([$user['USER_TYPE_ID']]);
            $typeInfo = $stmtType->fetch(PDO::FETCH_ASSOC);

            $_SESSION['user'] = [
                'user_id' => bin2hex($user['USER_ID']),
                'user_name' => $user['USER_NAME'],
                'user_email' => $user['USER_EMAIL'],
                'type_id' => bin2hex($user['USER_TYPE_ID']),
                'user_type' => $typeInfo['USER_TYPE'] ?? null,
                'img_url' => $user['IMG_URL']
            ];

            session_regenerate_id(true);
            echo json_encode(['success' => true, 'message' => 'Login efetuado com sucesso!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Credenciais inválidas.']);
        }
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Erro: ' . $e->getMessage()]);
    }

    exit;
}
?>

<div class="form-container">
    <form id="login-form" class="form-box" method="POST">
        <h2>Login</h2>
        <input type="email" name="email" placeholder="Email" required />
        <input type="password" name="password" placeholder="Palavra-passe" required />
        <button type="submit">Entrar</button>
        <div id="login-msg" style="margin-top: 10px;"></div>
        <p>Não tem conta? <a href="#" onclick="loadPage('register')">Criar conta</a></p>
    </form>
</div>
