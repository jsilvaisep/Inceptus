
<?php
$host = '143.47.56.69';
$port = '3306';
$dbname = 'DB_INCEPTUS_PP';
$user = '1241622';
$pass = 'jIr[77o06/';

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die('Erro na ligação à base de dados: ' . $e->getMessage());
}
$stmt=null;
?>
