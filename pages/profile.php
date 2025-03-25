<?php
session_start();
include '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');

    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['success' => false, 'message' => 'Acesso negado.']);
        exit;
    }

    $user_id = $_SESSION['user_id'];
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $imgPath = null;

    if (!$name || !$email) {
        echo json_encode(['success' => false, 'message' => 'Preencha nome e email.']);
        exit;
    }

    // Upload imagem
    if (isset($_FILES['profile_img']) && $_FILES['profile_img']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../uploads/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

        $filename = uniqid() . '_' . basename($_FILES['profile_img']['name']);
        $targetPath = $uploadDir . $filename;

        if (move_uploaded_file($_FILES['profile_img']['tmp_name'], $targetPath)) {
            $imgPath = 'uploads/' . $filename;
        }
    }

    try {
        $query = "UPDATE USER SET USER_NAME = ?, USER_EMAIL = ?";
        $params = [$name, $email];

        if (!empty($password)) {
            $query .= ", USER_PASSWORD = ?";
            $params[] = password_hash($password, PASSWORD_DEFAULT);
        }

        if ($imgPath) {
            $query .= ", IMG_URL = ?";
            $params[] = $imgPath;
            $_SESSION['user_img'] = $imgPath;
        }

        $query .= " WHERE USER_ID = ?";
        $params[] = $user_id;

        $stmt = $pdo->prepare($query);
        $stmt->execute($params);

        $_SESSION['user_name'] = $name;
        $_SESSION['user_email'] = $email;

        echo json_encode(['success' => true, 'message' => 'Perfil atualizado com sucesso!']);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Erro: ' . $e->getMessage()]);
    }
    exit;
}

if (!isset($_SESSION['user_id'])) {
    echo "<p class='error'>Acesso não autorizado.</p>";
    exit;
}

$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM USER WHERE USER_ID = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<div class="profile-container">
    <div class="profile-card">
        <h2>Editar Perfil</h2>
        <img src="<?= htmlspecialchars($user['IMG_URL']) ?: 'assets/img/default-user.png' ?>" class="profile-avatar" id="avatar-preview">
        <form id="edit-profile-form" enctype="multipart/form-data">
            <input type="text" name="name" value="<?= htmlspecialchars($user['USER_NAME']) ?>" required>
            <input type="email" name="email" value="<?= htmlspecialchars($user['USER_EMAIL']) ?>" required>
            <input type="password" name="password" placeholder="Nova senha (opcional)">
            <input type="file" name="profile_img" accept="image/*" onchange="previewAvatar(event)">
            <button type="submit">Guardar</button>
            <div id="profile-msg"></div>
        </form>
    </div>
</div>

<script>
function previewAvatar(event) {
    const output = document.getElementById('avatar-preview');
    output.src = URL.createObjectURL(event.target.files[0]);
}
</script>
