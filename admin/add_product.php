<?php
require_once '../config.php';
require_once '../functions.php';

if (!isAdmin()) {
    header('Location: ../index.php');
    exit;
}

$championships = getChampionships($pdo);
$championship_id = $_GET['championship_id'] ?? '';
$categories = [];

if ($championship_id) {
    $categories = getCategories($pdo, $championship_id);
}

$theme = getActiveTheme($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_product'])) {
    $product_id = $pdo->lastInsertId();
    
    $stmt = $pdo->prepare("INSERT INTO products (name, team, price, description, category_id, championship_id) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        $_POST['name'],
        $_POST['team'],
        $_POST['price'],
        $_POST['description'],
        $_POST['category_id'],
        $_POST['championship_id']
    ]);
    
    $product_id = $pdo->lastInsertId();
    
    // Upload images
    if (!is_dir('../uploads')) mkdir('../uploads', 0755, true);
    
    if (isset($_FILES['images'])) {
        foreach ($_FILES['images']['tmp_name'] as $key => $tmp) {
            if ($tmp) {
                $fileName = uniqid() . '_' . preg_replace('/[^a-zA-Z0-9.]/', '_', $_FILES['images']['name'][$key]);
                if (move_uploaded_file($tmp, '../uploads/' . $fileName)) {
                    addProductImage($pdo, $product_id, $fileName, $key === 0);
                }
            }
        }
    }
    
    header('Location: dashboard.php?tab=products');
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un maillot - SUNU Admin</title>
    <link rel="stylesheet" href="../styles.css">
    <style>
        :root {
            --primary: <?php echo $theme['color_primary']; ?>;
            --secondary: <?php echo $theme['color_secondary']; ?>;
        }
        .form-container { max-width: 600px; margin: 50px auto; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 8px; font-weight: bold; }
        .form-group input, .form-group textarea, .form-group select { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; }
        .form-group input:focus, .form-group textarea:focus, .form-group select:focus { outline: none; border-color: var(--primary); box-shadow: 0 0 5px var(--primary); }
        .btn-submit { background: var(--primary); color: white; padding: 12px 30px; border: none; border-radius: 5px; cursor: pointer; font-weight: bold; width: 100%; }
        .btn-submit:hover { background: var(--secondary); }
    </style>
</head>
<body>
    <div class="form-container">
        <a href="dashboard.php?tab=products" style="color: var(--primary); text-decoration: none;">← Retour</a>
        <h1>Ajouter un maillot</h1>
        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label>Championnat *</label>
                <select name="championship_id" required onchange="location.href='?championship_id=' + this.value;">
                    <option value="">Sélectionner un championnat</option>
                    <?php foreach ($championships as $champ): ?>
                        <option value="<?php echo $champ['id']; ?>" <?php echo $championship_id == $champ['id'] ? 'selected' : ''; ?>><?php echo $champ['name']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label>Catégorie (Équipe) *</label>
                <select name="category_id" required>
                    <option value="">Sélectionner une équipe</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?php echo $cat['id']; ?>"><?php echo $cat['name']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Nom du maillot *</label>
                <input type="text" name="name" required placeholder="Ex: Maillot Domicile 2024">
            </div>

            <div class="form-group">
                <label>Équipe *</label>
                <input type="text" name="team" required placeholder="Ex: Arsenal">
            </div>

            <div class="form-group">
                <label>Prix (€) *</label>
                <input type="number" name="price" step="0.01" required placeholder="49.99">
            </div>

            <div class="form-group">
                <label>Description</label>
                <textarea name="description" rows="4" placeholder="Décrivez le maillot..."></textarea>
            </div>

            <div class="form-group">
                <label>Photos (1 ou plusieurs) *</label>
                <input type="file" name="images[]" multiple accept="image/*" required>
            </div>

            <button type="submit" name="add_product" class="btn-submit">Ajouter le maillot</button>
        </form>
    </div>
</body>
</html>