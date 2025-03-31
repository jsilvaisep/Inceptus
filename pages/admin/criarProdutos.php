<?php
$host = '143.47.56.69';
$port = '3306';
$dbname = 'DB_INCEPTUS_PP';
$user = 'vaadin_user';
$pass = '#"6o6VB7!2';
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    try {
        $conn = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8", $user, $pass);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $stmt = $conn->prepare("CALL INSERT_PRODUCT(:product_name, :product_description, :category_id, :company_id)");
        $stmt->bindParam(':product_name', $_POST['product_name']);
        $stmt->bindParam(':product_description', $_POST['product_description']);
        $stmt->bindParam(':category_id', $_POST['category_id']);
        $company_id = "4ce516e6-0be9-11f0-b0d3-020017000d59";
        $stmt->bindParam(':company_id', $company_id, PDO::PARAM_INT);
        

        $stmt->execute();
        echo "<p class='success'Produto inserido com sucesso!</p>";
    } catch (PDOException $e) {
        echo "<p class='error'>Error: " . $e->getMessage() . "</p>";
    }
    $conn = null;   
}
// Fetch categories from the database
try {
    $conn = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8", $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $query = $conn->query("SELECT CATEGORY_ID, CATEGORY_NAME FROM CATEGORY");
    $categories = $query->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $categories = [];
}


?>

<!DOCTYPE html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Insert Product</title>
    <link rel="stylesheet" href="assets/css/criarProdutos.css">
    <script defer src="app.js"></script>
</head>
<body>
    <div class="container">
        <h2>Novo Produto</h2>
        <form id="productForm" action="/pages/admin/criarProdutos.php" method="POST">
            <label for="product_name">Nome do Produto:</label>
            <input type="text" id="product_name" name="product_name" required>

            <label for="product_description">Descrição do Produto:</label>
            <textarea id="product_description" name="product_description" required></textarea>

            <label for="category_id">Categoria:</label>
            <select id="category_id" name="category_id" required>
                <option value="">Selecione a categoria</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?php echo $category['CATEGORY_ID']; ?>">
                        <?php echo htmlspecialchars($category['CATEGORY_NAME']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <button type="submit">Submit</button>
        </form>
    </div>
</body>
</html>
