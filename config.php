<?php
// Configuration de la base de données XAMPP
$host = 'localhost';
$db_name = 'sunu_jersey';
$db_user = 'root';
$db_pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db_name;charset=utf8mb4", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die('❌ Erreur: ' . $e->getMessage());
}

define('SITE_NAME', 'SUNU');
define('SITE_TAGLINE', 'Plus qu\'un maillot, une passion');
define('WHATSAPP_SELLER_1', '212788617023');
define('WHATSAPP_SELLER_2', '212789263556');
define('MAX_QUANTITY', 5);

session_start();
?>