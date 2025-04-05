<?php
session_start();
include '../includes/db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = isset($_SESSION['user']['user_id']) ? hex2bin($_SESSION['user']['user_id']) : null;
    $userName = $_POST['user_name'] ?? '';
    $userEmail = $_POST['user_email'] ?? '';
    $isCompany = ($_SESSION['user']['user_type'] ?? '') === 'COMPANY';
    $imgPath = $_SESSION['user']['img_url'] ?? null;

    if (!$userId) {
        echo json_encode(['success' => false, 'message' => 'Utilizador não autenticado.']);
        exit;
    }

    // Atualiza imagem de perfil (se enviada)
    if (isset($_FILES['profile_img']) && $_FILES['profile_img']['error'] === UPLOAD_ERR_OK) {
        // Verifica se é uma imagem diferente da atual
        $isNewImage = true;

        if ($imgPath) {
            // Extrair apenas o nome do arquivo da imagem atual
            $currentImagePath = $_SERVER['DOCUMENT_ROOT'] . '/' . $imgPath;

            // Calcular hash da imagem atual (se existir)
            if (file_exists($currentImagePath)) {
                $currentHash = md5_file($currentImagePath);
                $newHash = md5_file($_FILES['profile_img']['tmp_name']);

                // Se os hashes forem iguais, é a mesma imagem
                if ($currentHash === $newHash) {
                    $isNewImage = false;
                }
            }
        }

        // Só processa o upload se for uma imagem diferente
        if ($isNewImage) {
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
    }

    try {
        // Atualizar dados do utilizador
        $stmt = $pdo->prepare("UPDATE USER SET USER_NAME = ?, USER_EMAIL = ?, IMG_URL = ? WHERE USER_ID = ?");
        $stmt->execute([$userName, $userEmail, $imgPath, $userId]);

        // Atualizar dados da empresa se aplicável
        if ($isCompany) {
            $companyName = $_POST['company_name'] ?? '';
            $companyEmail = $_POST['company_email'] ?? '';
            $companySite = $_POST['company_site'] ?? '';

            $stmt = $pdo->prepare("UPDATE COMPANY SET COMPANY_NAME = ?, COMPANY_EMAIL = ?, COMPANY_SITE = ? WHERE USER_ID = ?");
            $stmt->execute([$companyName, $companyEmail, $companySite, $userId]);
        }

        // Atualizar sessão
        $_SESSION['user']['user_name'] = $userName;
        $_SESSION['user']['user_email'] = $userEmail;
        $_SESSION['user']['img_url'] = $imgPath;

        echo json_encode(['success' => true, 'message' => 'Perfil atualizado com sucesso.']);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Erro: ' . $e->getMessage()]);
    }
    exit;
}

// GET: Mostrar formulário
$user = $_SESSION['user'] ?? [];
$isCompany = ($user['user_type'] ?? '') === 'COMPANY';

$companyData = null;
if ($isCompany) {
    $stmt = $pdo->prepare("SELECT COMPANY_NAME, COMPANY_EMAIL, COMPANY_SITE FROM COMPANY WHERE USER_ID = ?");
    $stmt->execute([hex2bin($user['user_id'] ?? '')]);
    $companyData = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<div class="profile-container">
    <form id="profile-form" class="profile-card" method="POST" enctype="multipart/form-data">
        <img src="<?= htmlspecialchars($user['img_url'] ?? 'assets/img/default-user.png') ?>" class="profile-avatar" id="avatar-preview">
        <input type="file" name="profile_img" accept="image/*" onchange="previewImage(event)">

        <input type="text" name="user_name" value="<?= htmlspecialchars($user['user_name'] ?? '') ?>" required placeholder="Nome">
        <input type="email" name="user_email"
       value="<?= isset($user['user_email']) ? htmlspecialchars($user['user_email']) : '' ?>"
       placeholder="Email"
       <?= isset($user['user_email']) && $user['user_email'] !== '' ? 'required' : '' ?>>


        <?php if ($isCompany && $companyData): ?>
            <input type="text" name="company_name" value="<?= htmlspecialchars($companyData['COMPANY_NAME'] ?? '') ?>" required placeholder="Nome da Empresa">
            <input type="email" name="company_email" value="<?= htmlspecialchars($companyData['COMPANY_EMAIL'] ?? '') ?>" required placeholder="Email da Empresa">
            <input type="url" name="company_site" value="<?= htmlspecialchars($companyData['COMPANY_SITE'] ?? '') ?>" required placeholder="Website da Empresa">
        <?php endif; ?>

        <button type="submit">Atualizar Perfil</button>
        <p id="profile-msg" class="msg"></p>
    </form>
</div>

<script>
function previewImage(event) {
    const preview = document.getElementById('avatar-preview');
    preview.src = URL.createObjectURL(event.target.files[0]);
}

document.getElementById('profile-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    const msg = document.getElementById('profile-msg');
    msg.textContent = 'A atualizar...';

    try {
        const res = await fetch('pages/profile.php', {
            method: 'POST',
            body: formData
        });

        const data = await res.json();
        msg.textContent = data.message;
        msg.className = data.success ? 'success' : 'error';

        if (data.success) {
            reloadNavbar();
        }
    } catch {
        msg.textContent = 'Erro no servidor.';
        msg.className = 'error';
    }
});
</script>
