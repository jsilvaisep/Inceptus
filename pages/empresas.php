<?php
include '../includes/db.php';

$comments = $pdo->prepare("CALL COMPANY_TOP");
$comments->execute();

foreach ($comments as $comment){

}
echo '
<div>
    <h2>Empresas</h2><p>Lista de empresas aqui.</p>
    <table>
        <tr>
            <th>Nome Empresa</th>
            <th>Morada</th>
        </tr>
        <tr>';
            echo '<td>Teste</td>';
        '</tr>
    </table>
</div>
'
?>