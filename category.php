<?php
require_once 'config.php';
require_once 'functions.php';

$category_id = $_GET['id'] ?? 0;
if (!$category_id) {
    header('Location: index.php');
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM categories WHERE id = ?");
$stmt->execute([$category_id]);
$category = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$category) {
    header('Location: index.php');
    exit;
}

$products = getProducts($pdo, $category_id);
$theme = getActiveTheme($pdo);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $category['name']; ?> - SUNU</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        :root {
            --primary: <?php echo $theme['color_primary']; ?>;
            --secondary: <?php echo $theme['color_secondary']; ?>;
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="container">
            <a href="index.php" style="color: white; text-decoration: none; font-size: 24px;">← SUNU</a>
        </div>
    </header>

    <div class="container" style="padding-top: 40px; padding-bottom: 40px;">
        <h1 style="color: var(--primary); margin-bottom: 30px;"><?php echo $category['name']; ?></h1>
        
        <?php if (empty($products)): ?>
            <p style="text-align: center; color: #999;">Aucun maillot disponible pour le moment.</p>
        <?php else: ?>
            <div class="products-grid">
                <?php foreach ($products as $product): ?>
                    <?php $images = getProductImages($pdo, $product['id']); ?>
                    <div class="product-card">
                        <?php if (!empty($images)): ?>
                            <img src="uploads/<?php echo htmlspecialchars($images[0]['image_path']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" style="width: 100%; height: 250px; object-fit: cover;">
                        <?php endif; ?>
                        <div style="padding: 15px;">
                            <h3 style="color: var(--primary); margin-bottom: 5px;"><?php echo htmlspecialchars($product['name']); ?></h3>
                            <p style="color: #666; margin-bottom: 10px;"><?php echo htmlspecialchars($product['team']); ?></p>
                            <p style="color: #27ae60; font-weight: bold; margin-bottom: 15px;"><?php echo number_format($product['price'], 2); ?>€</p>
                            <a href="product.php?id=<?php echo $product['id']; ?>" style="display: block; background: var(--primary); color: white; text-decoration: none; text-align: center; padding: 10px; border-radius: 5px;">Voir détails</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>