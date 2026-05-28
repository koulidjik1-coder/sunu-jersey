<?php
require_once '../config.php';
require_once '../functions.php';

if (!isAdmin()) {
    header('Location: ../index.php');
    exit;
}

$offer_id = $_GET['id'] ?? 0;
$offer = getOfferById($pdo, $offer_id);
$products = getProducts($pdo);
$theme = getActiveTheme($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_offer'])) {
    $image_path = '';
    $background_path = '';
    
    if (isset($_FILES['image']) && $_FILES['image']['size'] > 0) {
        $fileName = uniqid() . '_' . preg_replace('/[^a-zA-Z0-9.]/', '_', $_FILES['image']['name']);
        if (move_uploaded_file($_FILES['image']['tmp_name'], '../uploads/' . $fileName)) {
            $image_path = $fileName;
        }
    }
    
    if (isset($_FILES['background']) && $_FILES['background']['size'] > 0) {
        $fileName = uniqid() . '_' . preg_replace('/[^a-zA-Z0-9.]/', '_', $_FILES['background']['name']);
        if (move_uploaded_file($_FILES['background']['tmp_name'], '../uploads/' . $fileName)) {
            $background_path = $fileName;
        }
    }
    
    updateOffer($pdo, $offer_id, $_POST['name'], $_POST['description'], $image_path, $background_path, $_POST['link_product_id'] ?: null);
    header('Location: dashboard.php?tab=offers');
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier offre - Admin</title>
    <link rel="stylesheet" href="../styles.css">
    <style>
        :root {
            --primary: <?php echo $theme['color_primary']; ?>;
        }
        .form-container { max-width: 600px; margin: 50px auto; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 8px; font-weight: bold; }
        .form-group input, .form-group textarea, .form-group select { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; }
        .btn-submit { background: var(--primary); color: white; padding: 12px 30px; border: none; border-radius: 5px; cursor: pointer; font-weight: bold; width: 100%; }
        .preview { max-width: 200px; margin-top: 10px; border-radius: 5px; }
    </style>
</head>
<body>
    <div class="form-container">
        <a href="dashboard.php?tab=offers" style="color: var(--primary); text-decoration: none;">← Retour</a>
        <h1>Modifier offre</h1>
        
        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label>Nom de l'offre *</label>
                <input type="text" name="name" value="<?php echo htmlspecialchars($offer['name']); ?>" required>
            </div>
            
            <div class="form-group">
                <label>Description</label>
                <textarea name="description" rows="3"><?php echo htmlspecialchars($offer['description']); ?></textarea>
            </div>
            
            <div class="form-group">
                <label>Image de l'offre</label>
                <?php if ($offer['image_path']): ?>
                    <div>
                        <img src="../uploads/<?php echo $offer['image_path']; ?>" alt="" class="preview">
                        <p style="font-size: 12px; color: #666;">Image actuelle</p>
                    </div>
                <?php endif; ?>
                <input type="file" name="image" accept="image/*">
                <p style="font-size: 12px; color: #666;">Laisser vide pour garder l'image actuelle</p>
            </div>
            
            <div class="form-group">
                <label>Arrière-plan (photo)</label>
                <?php if ($offer['background_path']): ?>
                    <div>
                        <img src="../uploads/<?php echo $offer['background_path']; ?>" alt="" class="preview">
                        <p style="font-size: 12px; color: #666;">Arrière-plan actuel</p>
                    </div>
                <?php endif; ?>
                <input type="file" name="background" accept="image/*">
                <p style="font-size: 12px; color: #666;">Laisser vide pour garder l'arrière-plan actuel</p>
            </div>
            
            <div class="form-group">
                <label>Lier un produit (optionnel)</label>
                <select name="link_product_id">
                    <option value="">Aucun produit</option>
                    <?php foreach ($products as $product): ?>
                        <option value="<?php echo $product['id']; ?>" <?php echo $offer['link_product_id'] == $product['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($product['name']); ?> - <?php echo htmlspecialchars($product['team']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <button type="submit" name="update_offer" class="btn-submit">Mettre à jour l'offre</button>
        </form>
    </div>
</body>
</html>