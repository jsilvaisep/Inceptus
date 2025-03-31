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
        $stmt = $pdo->prepare("SELECT u.USER_ID, u.USER_NAME, u.USER_PASSWORD, u.USER_EMAIL, u.IMG_URL, ut2.TYPE_ID FROM USER u"+
							  "INNER JOIN U_TYPE ut ON u.USER_ID = ut.USER_ID"+
							  "INNER JOIN USER_TYPE ut2 ON ut2.TYPE_ID"+
							  "WHERE USER_EMAIL = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['u.USER_PASSWORD'])) {
            $_SESSION['user'] = [
                'user_id' => $user['u.USER_ID'],
                'user_name' => $user['u.USER_NAME'],
                'user_type' => getUserTypeName($pdo, $user['ut2.TYPE_ID']),
                'user_img' => $user['u.IMG_URL']
            ];

            session_regenerate_id(true);
            echo json_encode(['success' => true, 'message' => 'Login efetuado com sucesso!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Credenciais inválidas.']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Erro: ' . $e->getMessage()]);
    }

    $stmt = null;
    exit;
}

function getUserTypeName($pdo, $typeId) {
    $stmt = $pdo->prepare(	"SELECT ut2.USER_TYPE FROM USER u"+
							"INNER JOIN U_TYPE ut ON u.USER_ID = ut.USER_ID"+
							"INNER JOIN USER_TYPE ut2 ON ut2.TYPE_ID"+
							"WHERE ut2.TYPE_ID= ?");
    $stmt->execute([$typeId]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result ? $result['ut2.USER_TYPE'] : null;
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
