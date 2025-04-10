<?php

// TO DO: Ao selecionar mais de 1 imagem dá erro, provavelmente por causa do campo na db

include __DIR__ . '/db.php';
ob_clean();
header('Content-Type: application/json');
session_start();
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['product_name'])) {
    $uploadDir = __DIR__ . "/../produtos/"; // Path to "produtos/" inside project
    $uploadedFiles = [];

    // Handle image uploads (minimum 1, maximum 5)
    if (!empty($_FILES["product_images"]["name"][0])) {
        foreach ($_FILES["product_images"]["tmp_name"] as $key => $tmp_name) {
            if ($key >= 5)
                break; // Limit to 5 images

            $originalName = $_FILES["product_images"]["name"][$key];
            $extension = pathinfo($originalName, PATHINFO_EXTENSION);
            $safeName = preg_replace("/[^a-zA-Z0-9]/", "_", pathinfo($originalName, PATHINFO_FILENAME)); // Remove spaces
            $newFileName = $safeName . "_" . time() . "." . $extension;

            $destination = $uploadDir . $newFileName;
            if (move_uploaded_file($tmp_name, $destination)) {
                $uploadedFiles[] = "/../produtos/" . $newFileName; // Save relative path
            }
        }
    }

    if (empty($uploadedFiles)) {
        throw new Exception("At least one image is required.");
    }

    // Processar o campo de atributos para JSON
    $json_attributes = null;
    if (isset($_POST['product_attributes']) && !empty($_POST['product_attributes'])) {
        $string = $_POST['product_attributes'];
        $partes = explode(",", $string);
        $resultado = [];

        foreach ($partes as $parte) {
            list($chave, $valor) = explode(":", trim($parte), 2);
            $chave = trim($chave);
            $valor = trim($valor);
            $resultado[$chave] = $valor;
        }

        $json_attributes = json_encode($resultado);
    }

    if(isset($_POST['product-id-editar'])){
        $product = $_POST['product-id-editar'];
        $imagePaths = implode(";", $uploadedFiles);
        try {
            $stmt = $pdo->prepare("
                UPDATE PRODUCT 
                SET product_name = :product_name, 
                    product_description = :product_description, 
                    category_id = :category_id, 
                    img_url = :img_url,
                    attributes_json = :attributes_json
                WHERE product_id = :product_id
            ");
            $stmt->bindParam(':product_name', $_POST['product_name']);
            $stmt->bindParam(':product_description', $_POST['product_description']);
            $stmt->bindParam(':category_id', $_POST['category_id']);
            $stmt->bindParam(':img_url', $imagePaths);
            $stmt->bindParam(':attributes_json', $json_attributes);
            $stmt->bindParam(':product_id', $product);


            $stmt->execute();
            echo "<p class='success'>Produto editado com sucesso!</p>";
        } catch (PDOException $e) {
            echo "<p class='error'>Error: " . $e->getMessage() . "</p>";
        }
    }
    else{
        $user_id = $_SESSION['user']['user_id'];
        try {
            $stmt = $pdo->prepare("SELECT u.USER_ID, c.COMPANY_ID
                           FROM USER u 
                           INNER JOIN COMPANY c ON c.USER_ID = u.USER_ID
                           WHERE u.USER_ID = ? ");
            $stmt->execute([$user_id]);
            $company_id_result = $stmt->fetch(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            echo "" . $e->getMessage();
        }

        if (empty($company_id_result)) {
            echo '<p>Empresa inválida.</p>';
            exit;
        }

        $company_id = $company_id_result['COMPANY_ID'];
        $imagePaths = implode(";", $uploadedFiles);

        try {
            $stmt = $pdo->prepare("CALL INSERT_PRODUCT(:product_name, :product_description, :category_id, :company_id, :img_url, :attributes_json)");
            $stmt->bindParam(':product_name', $_POST['product_name']);
            $stmt->bindParam(':product_description', $_POST['product_description']);
            $stmt->bindParam(':category_id', $_POST['category_id']);
            $stmt->bindParam(':company_id', $company_id);
            $stmt->bindParam(':img_url', $imagePaths);
            $stmt->bindParam(':attributes_json', $json_attributes);

            $stmt->execute();
            echo "<p class='success'>Produto inserido com sucesso!</p>";
        } catch (PDOException $e) {
            echo "<p class='error'>Error: " . $e->getMessage() . "</p>";
        }
    }

}
// Fetch categories from the database
try {
    $query = $pdo->query("SELECT CATEGORY_ID, CATEGORY_NAME FROM CATEGORY");
    $categories = $query->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $categories = [];
}
$pdo = null;
$stmt = null;
?>

<!-- Floating modal container -->
<div id="modalOverlay" class="modal-overlay" style="display: none;">
    <div class="modal-content">
        <button id="closeModal" class="close-btn">&times;</button>
        <h2>Novo Produto</h2>
        <form id="productForm" method="POST" enctype="multipart/form-data" class="criarProdutoForm">
            <input type="hidden" name="action" id="formAction" value="">

            <label for="product_name">Nome do Produto:</label>
            <input type="text" id="product_name" name="product_name" required>

            <label for="product_description">Descrição do Produto:</label>
            <textarea id="product_description" name="product_description" required></textarea>

            <label for="category_id">Categoria:</label>
            <select id="category_id" name="category_id" required>
                <option value="">Selecione a categoria</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?php echo htmlspecialchars($category['CATEGORY_ID']); ?>">
                        <?php echo htmlspecialchars($category['CATEGORY_NAME']); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="product_attributes">Atributos do Produto (formato "chave: valor, chave: valor"):</label>
            <textarea id="product_attributes" name="product_attributes" placeholder="Ex: motor: 500w, peso: 500gr, bateria: 500mah"></textarea>
            <p class="attribute-note">Informe os atributos no formato "nome: valor, nome: valor"</p>

            <label for="product_images">Imagens do Produto (mínimo 1, máximo 5):</label>
            <input type="file" id="product_images" name="product_images[]" accept="image/*" multiple required>
            <p class="image-note">Máximo de 5 imagens. Apenas formatos JPG, PNG e GIF.</p>
            <div id="preview-container"></div>

            <button type="submit">Submit</button>
        </form>
    </div>
</div>