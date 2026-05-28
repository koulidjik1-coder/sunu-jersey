<?php
require_once '../config.php';
require_once '../functions.php';

if (!isAdmin()) {
    header('Location: ../index.php');
    exit;
}

$product_id = $_GET['id'] ?? 0;

// Supprimer les images
$stmt = $pdo->prepare("SELECT image_path FROM product_images WHERE product_id = ?");
$stmt->execute([$product_id]);
$images = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($images as $img) {
    $filePath = '../uploads/' . $img['image_path'];
    if (file_exists($filePath)) {
        unlink($filePath);
    }
}

// Supprimer le produit
$stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
$stmt->execute([$product_id]);

header('Location: dashboard.php?tab=products');
exit;
?>