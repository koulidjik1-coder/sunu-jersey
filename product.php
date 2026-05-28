<?php
require_once 'config.php';
require_once 'functions.php';

$product_id = $_GET['id'] ?? 0;
if (!$product_id) {
    header('Location: index.php');
    exit;
}

$product = getProductById($pdo, $product_id);
if (!$product) {
    header('Location: index.php');
    exit;
}

$images = getProductImages($pdo, $product_id);
$theme = getActiveTheme($pdo);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['name']); ?> - SUNU</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        :root {
            --primary: <?php echo $theme['color_primary']; ?>;
            --secondary: <?php echo $theme['color_secondary']; ?>;
        }
        .product-detail { display: grid; grid-template-columns: 1fr 1fr; gap: 40px; padding: 40px 0; }
        .product-images img { width: 100%; max-height: 400px; object-fit: cover; border-radius: 10px; }
        .thumbnails { display: flex; gap: 10px; margin-top: 15px; }
        .thumbnails img { width: 80px; height: 80px; object-fit: cover; border-radius: 5px; cursor: pointer; border: 2px solid transparent; transition: all 0.3s; }
        .thumbnails img:hover { border-color: var(--primary); }
        @media (max-width: 768px) { .product-detail { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
    <header class="header">
        <div class="container">
            <a href="index.php" style="color: white; text-decoration: none; font-size: 24px;">← SUNU</a>
        </div>
    </header>

    <div class="container">
        <div class="product-detail">
            <div class="product-images">
                <?php if (!empty($images)): ?>
                    <img src="uploads/<?php echo htmlspecialchars($images[0]['image_path']); ?>" id="main-image" alt="">
                    <?php if (count($images) > 1): ?>
                        <div class="thumbnails">
                            <?php foreach ($images as $img): ?>
                                <img src="uploads/<?php echo htmlspecialchars($img['image_path']); ?>" alt="" onclick="document.getElementById('main-image').src = this.src;">
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>

            <div class="product-info">
                <h1 style="color: var(--primary);"><?php echo htmlspecialchars($product['name']); ?></h1>
                <p style="color: #666; font-size: 18px; margin-bottom: 10px;">Équipe: <?php echo htmlspecialchars($product['team']); ?></p>
                <p style="color: #27ae60; font-size: 28px; font-weight: bold; margin-bottom: 20px;"><?php echo number_format($product['price'], 2); ?>€</p>
                <p style="color: #555; line-height: 1.6; margin-bottom: 30px;"><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>

                <div style="margin-bottom: 20px;">
                    <label style="font-weight: bold;">Quantité:</label>
                    <input type="number" id="quantity" value="1" min="1" max="5" style="width: 80px; padding: 8px; border: 1px solid #ddd; border-radius: 5px; margin-left: 10px;">
                </div>

                <button onclick="addToCart(<?php echo $product['id']; ?>, '<?php echo htmlspecialchars($product['name']); ?>', <?php echo $product['price']; ?>)" style="background: var(--primary); color: white; padding: 15px 40px; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; font-weight: bold; width: 100%;">Ajouter au panier</button>
            </div>
        </div>
    </div>

    <script>
        function addToCart(productId, productName, price) {
            let qty = parseInt(document.getElementById('quantity').value);
            let cart = JSON.parse(localStorage.getItem('cart')) || [];
            let existing = cart.find(item => item.id === productId);
            
            if (existing) {
                if (existing.quantity + qty > 5) {
                    alert('❌ Vous ne pouvez pas commander plus de 5 maillots du même type!');
                    return;
                }
                existing.quantity += qty;
            } else {
                if (qty > 5) {
                    alert('❌ Maximum 5 maillots par commande');
                    return;
                }
                cart.push({ id: productId, name: productName, price: price, quantity: qty });
            }
            
            localStorage.setItem('cart', JSON.stringify(cart));
            alert('✅ ' + qty + ' x ' + productName + ' ajouté au panier!');
            window.location.href = 'cart.php';
        }
    </script>
</body>
</html>