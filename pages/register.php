<?php
require 'vendor/autoload.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    include '../includes/db.php';
    header('Content-Type: application/json');

    $name = filter_var($_POST['name'] ?? '', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';
    $is_company = isset($_POST['is_company']) && $_POST['is_company'] === '1';
    $login = $_POST['login'] ?? '';
    $company_name = $_POST['company_name'] ?? '';
    $company_email = $_POST['company_email'] ?? '';
    $site = $_POST['site'] ?? '';

    if (!$name || !$email || !$password || !$confirm || !$login) {
        echo json_encode(['success' => false, 'message' => 'Preencha todos os campos obrigatórios.']);
        exit;
    }

    if ($password !== $confirm) {
        echo json_encode(['success' => false, 'message' => 'As palavras-passe não coincidem.']);
        exit;
    }

    try {
        $check = $pdo->prepare("SELECT USER_ID FROM USER WHERE USER_EMAIL = ?");
        $check->execute([$email]);
        if ($check->fetch()) {
            echo json_encode(['success' => true, 'message' => 'Conta já existente. Redirecionando para login...']);
            exit;
        }

        $imgPath = null;
        if (isset($_FILES['profile_img']) && $_FILES['profile_img']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/../uploads/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $ext = pathinfo($_FILES['profile_img']['name'], PATHINFO_EXTENSION);
            $filename = uniqid('img_', true) . '.' . $ext;
            $targetPath = $uploadDir . $filename;

            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
            $mimeType = mime_content_type($_FILES['profile_img']['tmp_name']);

            if (!in_array($mimeType, $allowedTypes)) {
                echo json_encode(['success' => false, 'message' => 'Tipo de imagem inválido.']);
                exit;
            }

            if (move_uploaded_file($_FILES['profile_img']['tmp_name'], $targetPath)) {
                $imgPath = 'uploads/' . $filename;
            } else {
                echo json_encode(['success' => false, 'message' => 'Erro ao guardar a imagem.']);
                exit;
            }
        }

        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        if ($is_company) {
            $stmt = $pdo->prepare("CALL INSERT_COMPANY (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$name, $company_name, $login, $email, $password_hash, $imgPath, $company_email, $site]);
        } else {
            $stmt = $pdo->prepare("CALL INSERT_USER (?, ?, ?, ?, ?)");
            $stmt->execute([$name, $login, $email, $password_hash, $imgPath]);
        }

        // Buscar o utilizador recém-criado para iniciar sessão
        $stmt = $pdo->prepare("SELECT USER_ID, USER_NAME, USER_TYPE_ID, IMG_URL FROM USER WHERE USER_EMAIL = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            $_SESSION['user'] = [
                'user_id' => bin2hex($user['USER_ID']),
                'user_name' => $user['USER_NAME'],
                'user_type' => $user['USER_TYPE_ID'],
                'img_url' => $user['IMG_URL']
            ];
        }

        echo json_encode(['success' => true, 'message' => 'Conta criada com sucesso.']);
        exit;

    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Erro: ' . $e->getMessage()]);
        exit;
    }
}
?>

<!-- HTML do formulário -->
<div class="form-container">
    <form class="form-box" id="register-form" method="POST" enctype="multipart/form-data">
        <h2>Criação de Conta</h2>
        <label for="name">Nome:</label>
        <input type="text" name="name" required>

        <label for="login">Username:</label>
        <input type="text" name="login" required>

        <label for="email">Email:</label>
        <input type="email" name="email" required>

        <label for="password">Palavra-passe:</label>
        <input type="password" name="password" required>

        <label for="confirm_password">Confirmar Palavra-passe:</label>
        <input type="password" name="confirm_password" required>

        <label for="profile_img">Foto de Perfil (opcional):</label>
        <input type="file" name="profile_img" accept="image/*">

        <label>
            <input type="checkbox" name="is_company" value="1" />
            Registar como empresa
        </label>

        <label for="company_name">Nome Empresa:</label>
        <input type="text" name="company_name">

        <label for="company_email">Email da Empresa:</label>
        <input type="email" name="company_email">

        <label for="site">Site Empresa:</label>
        <input type="url" name="site">

        <button type="submit">Criar Conta</button>
        <p id="register-msg" class="msg" style="margin-top: 10px;"></p>
    </form>
</div>
