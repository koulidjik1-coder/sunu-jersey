<?php
require_once '../config.php';
require_once '../functions.php';

if (!isAdmin()) {
    header('Location: ../index.php');
    exit;
}

if (isset($_POST['add'])) {
    addChampionship($pdo, $_POST['name']);
}

if (isset($_GET['action']) && $_GET['action'] === 'delete') {
    deleteChampionship($pdo, $_GET['id']);
}

header('Location: dashboard.php?tab=championships');
exit;
?>