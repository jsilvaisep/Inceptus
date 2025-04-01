<?php
session_start();
include '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');

    $name = trim($_POST['name'] ?? '');
    $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    $userType = 'SUSER';

    if (!$name || !$email || !$password || !$confirmPassword) {
        echo json_encode(['success' => false, 'message' => 'Preencha todos os campos.']);
        exit;
    }
    if ($password !== $confirmPassword) {
        echo json_encode(['success' => false, 'message' => 'As passwords não coincidem.']);
        exit;
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Email inválido.']);
        exit;
    }

    $imgPath = 'assets/img/default-user.png';
    if (!empty($_FILES['image']['name'])) {
        $targetDir = '../uploads/';
        $fileName = uniqid() . '_' . basename($_FILES['image']['name']);
        $targetFile = $targetDir . $fileName;
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
        $validTypes = ['jpg', 'jpeg', 'png', 'gif'];

        if (!in_array($imageFileType, $validTypes)) {
            echo json_encode(['success' => false, 'message' => 'Formato de imagem inválido.']);
            exit;
        }

        if ($_FILES['image']['size'] > 2 * 1024 * 1024) {
            echo json_encode(['success' => false, 'message' => 'Imagem muito grande. Máximo: 2MB.']);
            exit;
        }

        if (!move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
            echo json_encode(['success' => false, 'message' => 'Erro ao guardar a imagem.']);
            exit;
        }

        $imgPath = 'uploads/' . $fileName;
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO USER (USER_ID, USER_NAME, USER_EMAIL, USER_PASSWORD, USER_STATUS, IMG_URL, CREATED_AT, UPDATED_AT)
            VALUES (UUID_TO_BIN(UUID()), ?, ?, ?, 'A', ?, NOW(), NOW())");

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt->execute([$name, $email, $hashedPassword, $imgPath]);

        $stmtUser = $pdo->prepare("SELECT USER_ID FROM USER WHERE USER_EMAIL = ?");
        $stmtUser->execute([$email]);
        $userRow = $stmtUser->fetch(PDO::FETCH_ASSOC);
        $userId = $userRow['USER_ID'];

        $stmtType = $pdo->prepare("SELECT TYPE_ID FROM USER_TYPE WHERE USER_TYPE = ?");
        $stmtType->execute([$userType]);
        $type = $stmtType->fetch(PDO::FETCH_ASSOC);

        if ($type) {
            $stmtUT = $pdo->prepare("INSERT INTO U_TYPE (USER_ID, TYPE_ID) VALUES (?, ?)");
            $stmtUT->execute([$userId, $type['TYPE_ID']]);
        }

        $stmtInfo = $pdo->prepare("SELECT u.USER_ID, u.USER_NAME, u.USER_EMAIL, u.IMG_URL, ut2.USER_TYPE
            FROM USER u
            JOIN U_TYPE ut ON ut.USER_ID = u.USER_ID
            JOIN USER_TYPE ut2 ON ut.TYPE_ID = ut2.TYPE_ID
            WHERE u.USER_EMAIL = ?");
        $stmtInfo->execute([$email]);
        $user = $stmtInfo->fetch(PDO::FETCH_ASSOC);

        $_SESSION['user'] = [
            'user_id' => $user['USER_ID'],
            'user_name' => $user['USER_NAME'],
            'user_email' => $user['USER_EMAIL'],
            'user_img' => $user['IMG_URL'],
            'user_type' => $user['USER_TYPE']
        ];

        echo json_encode(['success' => true, 'message' => 'Conta criada com sucesso!']);
        exit;

    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Erro ao criar conta: ' . $e->getMessage()]);
        exit;
    }
}
?>

<!-- HTML do formulário -->
<div class="form-container">
    <form id="register-form" class="form-box" enctype="multipart/form-data">
        <h2>Criar Conta</h2>
        <input type="text" name="name" placeholder="Nome" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <input type="password" name="confirm_password" placeholder="Confirmar Password" required>
        <input type="file" name="image" accept="image/*">
        <button type="submit">Registar</button>
        <div id="register-msg"></div>
        <p>Já tem conta? <a href="#" onclick="loadPage('login')">Entrar</a></p>
    </form>
</div>

<script>
    document.getElementById('register-form').addEventListener('submit', function (e) {
        e.preventDefault();
        const formData = new FormData(this);
        fetch('../includes/auth.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            const msgDiv = document.getElementById('register-msg');
            msgDiv.textContent = data.message;
            if (data.success) {
                setTimeout(() => {
                    loadPage('home');
                }, 2000);
            }
        })
        .catch(error => console.error('Erro:', error));
    });