<?php
require_once '../config.php';
require_once '../functions.php';

if (!isAdmin()) {
    header('Location: ../index.php');
    exit;
}

$offer_id = $_GET['id'] ?? 0;
if ($offer_id) {
    $offer = getOfferById($pdo, $offer_id);
    if ($offer['image_path']) {
        @unlink('../uploads/' . $offer['image_path']);
    }
    if ($offer['background_path']) {
        @unlink('../uploads/' . $offer['background_path']);
    }
    deleteOffer($pdo, $offer_id);
}

header('Location: dashboard.php?tab=offers');
exit;
?>