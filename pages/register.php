<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    include '../includes/db.php';
    header('Content-Type: application/json');

    $name = filter_var($_POST['name'] ?? '', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

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
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
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

        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("CALL INSERT_USER(:name, :email, :password, :imgPath)");
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $hash);
        $stmt->bindParam(':imgPath', $imgPath);
        $stmt->execute();

        $userId = $pdo->lastInsertId();

        $_SESSION['user'] = [
            'user_id' => $userId,
            'user_name' => $name,
            'user_type' => 'SUSER',
            'user_img' => $imgPath
        ];

        session_regenerate_id(true);
        echo json_encode(['success' => true, 'message' => 'Registo efetuado com sucesso! Redirecionando...']);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Erro: ' . $e->getMessage()]);
    }

    $stmt = null;
    exit;
}
?>

<div class="form-container">
    <form id="register-form" class="form-box" enctype="multipart/form-data">
        <h2>Registo</h2>
        <input type="text" name="name" placeholder="Nome completo" required />
        <input type="email" name="email" placeholder="Email" required />
        <input type="password" name="password" placeholder="Password" required />
        <input type="password" name="confirm_password" placeholder="Confirmar Password" required />
        <label>Imagem de perfil: <input type="file" name="profile_img" accept="image/*" /></label>
        <label><input type="checkbox" id="terms" required /> Aceito os termos e condições</label>
        <button type="submit">Registar</button>
        <div id="register-msg"></div>
        <p>Já tem conta? <a href="?page=login">Entrar</a></p>
    </form>
</div>
