<?php
include '../../includes/db.php';
session_start();

if (!isset($_SESSION['user']) || ($_SESSION['user']['user_type'] !== 'ADMIN') ){
    echo "<div class='alert alert-danger'>Acesso restrito.</div>";
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT * FROM COMPANY c ORDER BY c.COMPANY_NAME DESC ");
    $stmt->execute();
    $companies = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo "erro" . $e->getMessage();
}

if (empty($companies)) {
    echo '<p>Sem empresas registados.</p>';
    exit;
}

?>

<div class="dash_list">
    <div class="dash_head">
        <h2 class="dash_title">Gestão de Empresas</h2>
        <button class="delete_button" onclick="loadPage('admin/dashboard')">Voltar</button>
    </div>
    <table class="dash_table">
        <tr class="dash_table_header">
            <th>Company Name</th>
            <th>Company email</th>
            <th>Company Site</th>
            <th>Company Rank</th>
            <th></th>
            <th></th>
        </tr>
        <?php foreach ($companies as $company): ?>
            <tr class="dash_table_data">
                <td><?= htmlspecialchars($company['COMPANY_NAME']) ?></td>
                <td><?= htmlspecialchars($company['COMPANY_EMAIL']) ?></td>
                <td><?= htmlspecialchars($company['COMPANY_SITE']) ?></td>
                <td><?= htmlspecialchars($company['COMPANY_RANK']) ?></td>
                <td>
                    <button class="edit_button"
                            onclick="submitEditarEmpresaAdmin('<?= htmlspecialchars($company['COMPANY_ID']) ?>')">Editar</button>
                </td>
                <td><button class="delete_button"
                            onclick="submitEliminarEmpresaAdmin('<?= htmlspecialchars($company['COMPANY_ID']) ?>')">Eliminar</button>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>