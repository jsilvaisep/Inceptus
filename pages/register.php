<?php
// session_start();

function validarSenha($senha) {
    $erros = [];

    // Verificar comprimento mínimo
    if (strlen($senha) < 8) {
        $erros[] = "A password deve ter pelo menos 10 caracteres";
    }

    // Verificar presença de letra minúscula
    if (!preg_match('/[a-z]/', $senha)) {
        $erros[] = "A password deve conter pelo menos uma letra minúscula";
    }

    // Verificar presença de letra maiúscula
    if (!preg_match('/[A-Z]/', $senha)) {
        $erros[] = "A password deve conter pelo menos uma letra maiúscula";
    }

    // Verificar presença de número
    if (!preg_match('/[0-9]/', $senha)) {
        $erros[] = "A password deve conter pelo menos um número";
    }

    // Verificar presença de caractere especial
    if (!preg_match('/[!@#$%^&*(),.?":{}|<>]/', $senha)) {
        $erros[] = "A password deve conter pelo menos um caracter especial";
    }

    return $erros;
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '../../vendor/autoload.php';
include '../includes/db.php';

function enviarEmailConfirmacao($nome, $email, $login)
{
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'maianetwork.cloud@gmail.com';
        $mail->Password = 'bnzp epzh nbgh wslb';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->CharSet = 'UTF-8';

        $mail->setFrom('maianetwork.cloud@gmail.com', 'Inceptus');
        $mail->addAddress($email, $nome);

        $mail->isHTML(true);
        $mail->Subject = 'Criação de Conta efetuada com sucesso';
        $mail->Body = <<<HTML
            <h2>Olá, {$nome}!</h2>
            <p>Sua conta foi criada com sucesso.</p>
            <p>Username : <strong>{$login}</strong></p>
            <p>Bem-vindo à nossa plataforma!</p>
        HTML;

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Erro ao enviar e-mail: {$mail->ErrorInfo}");
        return false;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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

    // Validar requisitos da senha
    $errosSenha = validarSenha($password);
    if (!empty($errosSenha)) {
        echo json_encode([
            'success' => false,
            'message' => 'Requisitos de password não cumpridos:',
            'errors' => $errosSenha
        ]);
        exit;
    }

    try {
        $check = $pdo->prepare("SELECT USER_ID FROM USER WHERE USER_EMAIL = ?");
        $check->execute([$email]);
        if ($check->fetch()) {
            echo json_encode(['success' => false, 'message' => 'Conta já existente. Redirecionando para login...']);
            exit;
        }

        // Upload da imagem de perfil
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

        // Inserir no banco
        if ($is_company) {
            $stmt = $pdo->prepare("CALL INSERT_COMPANY (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$name, $company_name, $login, $email, $password_hash, $imgPath, $company_email, $site]);
        } else {
            $stmt = $pdo->prepare("CALL INSERT_USER (?, ?, ?, ?, ?)");
            $stmt->execute([$name, $login, $email, $password_hash, $imgPath]);
        }

        // Buscar o utilizador para sessão
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

        // Enviar o e-mail
        if (enviarEmailConfirmacao($name, $email, $login)) {
            echo json_encode(['success' => true, 'message' => 'Conta criada com sucesso e e-mail enviado.']);
        } else {
            echo json_encode(['success' => true, 'message' => 'Conta criada, mas falha ao enviar e-mail.']);
        }
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