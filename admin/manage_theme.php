<?php
require_once '../config.php';
require_once '../functions.php';

if (!isAdmin()) {
    header('Location: ../index.php');
    exit;
}

$theme_id = $_GET['id'] ?? 0;
setActiveTheme($pdo, $theme_id);

header('Location: dashboard.php?tab=themes');
exit;
?>