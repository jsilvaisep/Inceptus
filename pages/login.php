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
        $stmt = $pdo->prepare(
            "SELECT u.USER_ID, u.USER_NAME, u.USER_PASSWORD, u.USER_EMAIL, u.IMG_URL, ut.TYPE_ID, ut2.USER_TYPE FROM USER u 
            INNER JOIN U_TYPE ut ON ut.USER_ID = u.USER_ID 
            INNER JOIN USER_TYPE ut2 ON ut.TYPE_ID = ut2.TYPE_ID
            WHERE USER_EMAIL = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['USER_PASSWORD'])) {
            $_SESSION['user'] = [
                'user_id' => $user['USER_ID'],
                'user_name' => $user['USER_NAME'],
                //'user_type' => getUserTypeName($pdo, $user['TYPE_ID']),
                'user_type' => $user['USER_TYPE'],
                'user_img' => $user['IMG_URL']
            ];

            session_regenerate_id(true);
            echo json_encode(['success' => true, 'message' => 'Login efetuado com sucesso!']);
			$stmt = null;
        } else {
            echo json_encode(['success' => false, 'message' => 'Credenciais inválidas.']);
			$stmt = null;
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Erro: ' . $e->getMessage()]);
		$stmt = null;
    }

    $stmt = null;
    exit;
}
/*
function getUserTypeName($pdo, $typeId) {
    $stmt = $pdo->prepare("SELECT USER_TYPE FROM USER_TYPE WHERE TYPE_ID = ?");
    $stmt->execute([$typeId]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result ? $result['USER_TYPE'] : null;
}*/
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
