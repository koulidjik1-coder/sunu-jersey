<?php
require_once '../config.php';
require_once '../functions.php';

if (!isAdmin()) {
    header('Location: ../index.php');
    exit;
}

$action = $_GET['action'] ?? '';
$user_id = $_GET['id'] ?? 0;

if ($action === 'make_admin') {
    makeAdmin($pdo, $user_id);
} elseif ($action === 'remove_admin') {
    removeAdmin($pdo, $user_id);
} elseif ($action === 'delete') {
    deleteUser($pdo, $user_id);
}

header('Location: dashboard.php?tab=users');
exit;
?>