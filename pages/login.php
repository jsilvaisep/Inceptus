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
        // Obter dados do utilizador
        $stmt = $pdo->prepare("SELECT * FROM USER WHERE USER_EMAIL = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['USER_PASSWORD'])) {
            // Obter o TYPE_ID real via tabela U_TYPE
            $stmtType = $pdo->prepare("SELECT T.TYPE_ID, UT.USER_TYPE 
                                       FROM U_TYPE T 
                                       JOIN USER_TYPE UT ON T.TYPE_ID = UT.TYPE_ID 
                                       WHERE T.USER_ID = ?");
            $stmtType->execute([$user['USER_ID']]);
            $typeInfo = $stmtType->fetch(PDO::FETCH_ASSOC);

            $_SESSION['user'] = [
                'user_id' => $user['USER_ID'],
                'user_name' => $user['USER_NAME'],
                'user_email' => $user['USER_EMAIL'],
                'type_id' => $typeInfo['TYPE_ID'] ?? null,
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

<!-- Formulário HTML de login -->
<div class="form-container">
    <form id="login-form" class="form-box" method="POST">
        <h2>Login</h2>
        <input type="email" name="email" placeholder="Email" required />
        <input type="password" name="password" placeholder="Palavra-passe" required />
        <button type="submit">Entrar</button>
        <div id="login-msg" style="margin-top: 10px;"></div>
        <p>Não tem conta? <a href="?page=register">Criar conta</a></p>
    </form>
</div>
