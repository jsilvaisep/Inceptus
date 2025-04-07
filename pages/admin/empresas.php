<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['user_type'] !== 'ADMIN') {
    echo "<div class='alert alert-danger'>Acesso restrito.</div>";
    exit;
}

require_once __DIR__ . '/../../includes/db.php';

$search = $_GET['search'] ?? '';
$searchTerm = '%' . $search . '%';
$page = isset($_GET['pg']) ? (int)$_GET['pg'] : 1;
$perPage = 10;
$offset = ($page - 1) * $perPage;

$query = "SELECT * FROM COMPANY C JOIN USER U ON C.USER_ID = U.USER_ID WHERE C.COMPANY_NAME LIKE ? ORDER BY C.COMPANY_NAME ASC LIMIT ? OFFSET ?";
$stmt = $pdo->prepare($query);
$stmt->execute([$searchTerm, $perPage, $offset]);
$empresas = $stmt->fetchAll(PDO::FETCH_ASSOC);

$totalStmt = $pdo->prepare("SELECT COUNT(*) FROM COMPANY");
$totalStmt->execute();
$totalRows = $totalStmt->fetchColumn();
$totalPages = ceil($totalRows / $perPage);

?>

<div class="container py-5">
    <h2 class="mb-4">Gestão de Empresas</h2>
    
    <div class="search-section mb-3">
        <form action="empresas.php" method="get">
            <input type="text" name="search" class="form-control" placeholder="Pesquisar empresas..." value="<?= htmlspecialchars($search) ?>" />
            <button type="submit" class="btn btn-primary mt-2">Pesquisar</button>
        </form>
    </div>
    
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Email</th>
                    <th>Site</th>
                    <th>Responsável</th>
                    <th>Email Responsável</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($empresas as $empresa): ?>
                    <tr>
                        <td><?= htmlspecialchars($empresa['COMPANY_NAME']) ?></td>
                        <td><?= htmlspecialchars($empresa['COMPANY_EMAIL']) ?></td>
                        <td><a href="<?= htmlspecialchars($empresa['COMPANY_SITE']) ?>" target="_blank">Visitar</a></td>
                        <td><?= htmlspecialchars($empresa['USER_NAME']) ?></td>
                        <td><?= htmlspecialchars($empresa['USER_EMAIL']) ?></td>
                        <td>
                            <button class="btn btn-sm btn-primary" onclick="openModal('edit', <?= $empresa['COMPANY_ID'] ?>)">Editar</button>
                            <button class="btn btn-sm btn-danger" onclick="deleteEmpresa(<?= $empresa['COMPANY_ID'] ?>)">Excluir</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    
    <div class="pagination">
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <a href="empresas.php?search=<?= urlencode($search) ?>&pg=<?= $i ?>" class="btn btn-outline-primary <?= $i == $page ? 'active' : '' ?>"><?= $i ?></a>
        <?php endfor; ?>
    </div>
</div>

<div id="modal-container"></div>

<script>
function openModal(type, companyId) {
    let modalContent = '';
    if (type === 'edit') {
        fetch('includes/admin/edit_empresa.php?id=' + companyId)
            .then(response => response.text())
            .then(data => {
                modalContent = data;
                document.getElementById('modal-container').innerHTML = modalContent;
                $('#modal-container').modal('show');
            });
    }
}

function deleteEmpresa(companyId) {
    if (confirm("Tem certeza que deseja eliminar esta empresa?")) {
        fetch('includes/admin/delete_empresa.php?id=' + companyId)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert("Empresa excluída com sucesso!");
                    window.location.reload();
                } else {
                    alert("Erro ao eliminar empresa.");
                }
            });
    }
}
</script>
