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
            $_SESSION['user_id'] = $user['USER_ID'];
            $_SESSION['user_name'] = $user['USER_NAME'];
            $_SESSION['user_type'] = $user['TYPE_ID'];
            $_SESSION['user_img'] = $user['IMG_URL'];

            session_regenerate_id(true);
            echo json_encode(['success' => true, 'message' => 'Login efetuado com sucesso!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Credenciais inválidas.']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Erro: ' . $e->getMessage()]);
    }
    $stmt=null;
    exit;
}
?>

<div class="form-container">
    <form id="login-form" class="form-box">
        <h2>Login</h2>
        <input type="email" name="email" placeholder="Email" required />
        <input type="password" name="password" placeholder="Password" required />
        <button type="submit">Entrar</button>
        <div id="login-msg"></div>
        <p>Não tem conta? <a href="?page=register">Criar conta</a></p>
    </form>
</div>
