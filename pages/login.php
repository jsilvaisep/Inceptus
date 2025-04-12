<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    include '../includes/db.php';
    header('Content-Type: application/json');

    $login = trim($_POST['login'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!$login || !$password) {
        echo json_encode(['success' => false, 'message' => 'Preencha todos os campos.']);
        exit;
    }

    try {
        // Buscar utilizador pelo login (sem verificar o status)
        $stmt = $pdo->prepare("SELECT * FROM USER WHERE USER_LOGIN = ?");
        $stmt->execute([$login]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Verificar se o utilizador existe e a senha está correta
        if ($user && password_verify($password, $user['USER_PASSWORD'])) {
            // Verificar se o utilizador está ativo
            if ($user['USER_STATUS'] === 'A') {
                $stmtType = $pdo->prepare("SELECT ut.USER_TYPE, ut.TYPE_ID
                                          FROM USER_TYPE ut
                                          WHERE ut.TYPE_ID = ?");
                $stmtType->execute([$user['USER_TYPE_ID']]);
                $typeInfo = $stmtType->fetch(PDO::FETCH_ASSOC);

                $_SESSION['user'] = [
                    'user_id' => $user['USER_ID'],
                    'user_name' => $user['USER_LOGIN'],
                    'user_email' => $user['USER_EMAIL'],
                    'type_id' => $user['USER_TYPE_ID'],
                    'user_type' => $typeInfo['USER_TYPE'] ?? null,
                    'img_url' => $user['IMG_URL']
                ];

                session_regenerate_id(true);
                echo json_encode(['success' => true, 'message' => 'Login efetuado com sucesso!']);
            } else {
                // Mensagem específica para conta inativa
                echo json_encode(['success' => false, 'message' => 'Esta conta está inativa. Por favor, contacte o administrador.']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Credenciais inválidas.']);
        }
        exit;
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Erro: ' . $e->getMessage()]);
    }

    exit;
}
?>

<!-- HTML do formulário -->
<div class="form-container">
    <form id="login-form" class="form-box" method="POST">
        <h2>Login</h2>
        <input type="text" name="login" placeholder="Username" required />
        <input type="password" name="password" placeholder="Palavra-passe" required />
        <button type="submit">Entrar</button>
        <div id="login-msg" style="margin-top: 10px;"></div>
        <p>Não tem conta? <a href="#" onclick="loadPage('register')">Criar conta</a></p>
    </form>
</div>