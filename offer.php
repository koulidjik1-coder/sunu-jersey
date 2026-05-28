<?php
require_once 'config.php';
require_once 'functions.php';

$offer_id = $_GET['id'] ?? 0;
if (!$offer_id) {
    header('Location: index.php');
    exit;
}

$offer = getOfferById($pdo, $offer_id);
if (!$offer) {
    header('Location: index.php');
    exit;
}

$product = null;
if ($offer['link_product_id']) {
    $product = getProductById($pdo, $offer['link_product_id']);
}

$images = $product ? getProductImages($pdo, $product['id']) : [];
$theme = getActiveTheme($pdo);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($offer['name']); ?> - SUNU</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        :root {
            --primary: <?php echo $theme['color_primary']; ?>;
            --secondary: <?php echo $theme['color_secondary']; ?>;
        }
        .offer-detail { padding: 40px 0; }
        .offer-hero { background: url('uploads/<?php echo htmlspecialchars($offer['background_path']); ?>') center/cover; color: white; padding: 80px 20px; text-align: center; border-radius: 10px; margin-bottom: 40px; }
        .offer-hero h1 { font-size: 36px; margin-bottom: 20px; }
        .product-detail { display: grid; grid-template-columns: 1fr 1fr; gap: 40px; }
        @media (max-width: 768px) { .product-detail { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
    <header class="header">
        <div class="container">
            <a href="index.php" style="color: white; text-decoration: none; font-size: 24px;">← SUNU</a>
        </div>
    </header>

    <div class="container offer-detail">
        <?php if ($offer['background_path']): ?>
            <div class="offer-hero">
                <h1><?php echo htmlspecialchars($offer['name']); ?></h1>
                <p><?php echo htmlspecialchars($offer['description']); ?></p>
            </div>
        <?php endif; ?>

        <?php if ($product): ?>
            <div class="product-detail">
                <div class="product-images">
                    <?php if (!empty($images)): ?>
                        <img src="uploads/<?php echo htmlspecialchars($images[0]['image_path']); ?>" id="main-image" alt="" style="width: 100%; height: 400px; object-fit: cover; border-radius: 10px;">
                        <?php if (count($images) > 1): ?>
                            <div style="display: flex; gap: 10px; margin-top: 15px;">
                                <?php foreach ($images as $img): ?>
                                    <img src="uploads/<?php echo htmlspecialchars($img['image_path']); ?>" alt="" style="width: 80px; height: 80px; object-fit: cover; border-radius: 5px; cursor: pointer;" onclick="document.getElementById('main-image').src = this.src;">
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
        <?php else: ?>
            <div style="text-align: center; padding: 40px;">
                <p style="color: #999;">Cette offre n'est pas liée à un produit.</p>
                <a href="index.php" style="color: var(--primary); text-decoration: none;">← Retour à l'accueil</a>
            </div>
        <?php endif; ?>
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