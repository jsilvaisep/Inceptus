<?php
session_start();
include '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');

    // Verifica se o usuário está logado
    if (!isset($_SESSION['user'])) {
        echo json_encode(['success' => false, 'message' => 'Acesso negado.']);
        exit;
    }

    // Recupera os dados do formulário
    $user_id = $_SESSION['user']['user_id'];
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $imgPath = $_SESSION['user']['user_img'];  // Mantém a imagem atual, caso não seja feita alteração
    $stmt = null;

    // Verifica se o nome e o email estão preenchidos
    if (!$name || !$email) {
        echo json_encode(['success' => false, 'message' => 'Preencha nome e email.']);
        exit;
    }

    // Faz o upload da nova imagem
    if (isset($_FILES['profile_img']) && $_FILES['profile_img']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/';  // Verifique se a pasta uploads existe e tem permissões corretas
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

        $filename = uniqid() . '_' . basename($_FILES['profile_img']['name']);
        $targetPath = $uploadDir . $filename;

        // Move o arquivo para o diretório
        if (move_uploaded_file($_FILES['profile_img']['tmp_name'], $targetPath)) {
            $imgPath = 'uploads/' . $filename;
        }
    }

    try {
        // Atualiza os dados no banco de dados
        $query = "UPDATE USER SET USER_NAME = ?, USER_EMAIL = ?";
        $params = [$name, $email];

        // Atualiza a senha, se fornecida
        if (!empty($password)) {
            $query .= ", USER_PASSWORD = ?";
            $params[] = password_hash($password, PASSWORD_DEFAULT);
        }

        // Atualiza a imagem, se fornecida
        if ($imgPath && $imgPath !== $_SESSION['user']['user_img']) {
            $query .= ", IMG_URL = ?";
            $params[] = $imgPath;
        }

        $query .= " WHERE USER_ID = ?";
        $params[] = $user_id;

        // Prepara e executa a consulta
        $stmt = $pdo->prepare($query);
        $stmt->execute($params);

        // Atualiza a sessão com os novos dados
        $_SESSION['user_name'] = $name;
        $_SESSION['user_email'] = $email;
        $_SESSION['user_img'] = $imgPath;

        echo json_encode(['success' => true, 'message' => 'Perfil atualizado com sucesso!']);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Erro: ' . $e->getMessage()]);
    }
    $stmt = null;
    exit;
}

// Verifica se o usuário está logado
if (!isset($_SESSION['user'])) {
    echo "<p class='error'>Acesso não autorizado.</p>";
    exit;
}

$user_id = $_SESSION['user']['user_id'];
$stmt = $pdo->prepare("SELECT * FROM USER WHERE USER_ID = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
$stmt = null;
?>

<div class="profile-container">
    <div class="profile-card">
        <h2>Editar Perfil</h2>
        <!-- Exibe a foto de perfil ou a foto padrão -->
        <img src="<?= $user['IMG_URL'] ?: 'assets/img/default-user.png' ?>" class="profile-avatar" id="avatar-preview">
        
        <!-- Formulário de edição do perfil -->
        <form id="edit-profile-form" enctype="multipart/form-data">
            <input type="text" name="name" value="<?= htmlspecialchars($user['USER_NAME']) ?>" required>
            <input type="email" name="email" value="<?= htmlspecialchars($user['USER_EMAIL']) ?>" required>
            <input type="password" name="password" placeholder="Nova Password (opcional)">
            <input type="file" name="profile_img" accept="image/*" onchange="previewAvatar(event)">
            <button type="submit">Guardar</button>
            <div id="profile-msg"></div>
        </form>
    </div>
</div>

<script>
    // Função para mostrar o preview da imagem antes do upload
    function previewAvatar(event) {
        const output = document.getElementById('avatar-preview');
        output.src = URL.createObjectURL(event.target.files[0]);
    }

    // Lida com o envio do formulário de edição de perfil
    const form = document.getElementById('edit-profile-form');
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(form);
        const msg = document.getElementById('profile-msg');
        
        fetch('profile.php', { method: 'POST', body: formData })
            .then(res => res.json())
            .then(data => {
                msg.innerHTML = `<p class="${data.success ? 'success' : 'error'}">${data.message}</p>`;
                if (data.success) {
                    setTimeout(() => {
                        window.location.reload();  // Recarrega a página após o sucesso da atualização
                    }, 1000);
                }
            })
            .catch(() => {
                msg.innerHTML = '<p class="error">Erro ao processar a atualização.</p>';
            });
    });
</script>
