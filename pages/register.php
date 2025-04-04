<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    include '../includes/db.php';
    header('Content-Type: application/json');

    $name = filter_var($_POST['name'] ?? '', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';
    $is_company = isset($_POST['is_company']) && $_POST['is_company'] === '1';

    if (!$name || !$email || !$password || !$confirm) {
        echo json_encode(['success' => false, 'message' => 'Preencha todos os campos.']);
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
            $uploadDir = '../uploads/';
            if (!is_dir($uploadDir))
                mkdir($uploadDir, 0755, true);
            $filename = uniqid() . '_' . basename($_FILES['profile_img']['name']);
            $targetPath = $uploadDir . $filename;

            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
            $mimeType = mime_content_type($_FILES['profile_img']['tmp_name']);

            if (!in_array($mimeType, $allowedTypes)) {
                echo json_encode(['success' => false, 'message' => 'Tipo de imagem inválido.']);
                exit;
            }

            if (move_uploaded_file($_FILES['profile_img']['tmp_name'], $targetPath)) {
                $imgPath = 'uploads/' . $filename;
            }
        }

        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        if ($is_company) {
            $stmt = $pdo->prepare("CALL INSERT_COMPANY (?, ?, ?, ?, ?, ?, ?)");
            $login = 'company_' . uniqid();
            $stmt->execute([$name, 'Nome da Empresa', $login, $email, $password_hash, $imgPath, $email]);
        } else {
           
           
           
           // TODO - Colocar um IF para empresa ou user normal "CALL INSERT_COMPANY (?, ?, ?, ?)"
            $stmt = $pdo->prepare("CALL INSERT_USER (?, ?, ?, ?, ?)");
            $login = 'user_' . uniqid();
            $stmt->execute([$name, $login, $email, $password_hash, $imgPath]);
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


<div class="form-container">
    <form class="form-box" id="register-form" method="POST" enctype="multipart/form-data">
        <h2>Criação de Conta</h2>
        <label for="name">Nome:</label>
        <input type="text" name="name" required>

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

        <button type="submit">Criar Conta</button>
        <p id="register-msg" class="msg" style="margin-top: 10px;"></p>
    </form>
</div>
