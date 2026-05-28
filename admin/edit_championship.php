<?php
require_once '../config.php';
require_once '../functions.php';

if (!isAdmin()) {
    header('Location: ../index.php');
    exit;
}

$championship_id = $_GET['id'] ?? 0;
if (!$championship_id) {
    header('Location: dashboard.php?tab=championships');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['logo'])) {
    if (!is_dir('../uploads')) mkdir('../uploads', 0755, true);
    
    $fileName = uniqid() . '_' . preg_replace('/[^a-zA-Z0-9.]/', '_', $_FILES['logo']['name']);
    if (move_uploaded_file($_FILES['logo']['tmp_name'], '../uploads/' . $fileName)) {
        updateChampionshipLogo($pdo, $championship_id, $fileName);
        header('Location: dashboard.php?tab=championships');
        exit;
    }
}

$championship = $pdo->prepare("SELECT * FROM championships WHERE id = ?")->fetch(PDO::FETCH_ASSOC);
$theme = getActiveTheme($pdo);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier championnat - Admin</title>
    <link rel="stylesheet" href="../styles.css">
    <style>
        :root {
            --primary: <?php echo $theme['color_primary']; ?>;
        }
        .form-container { max-width: 600px; margin: 50px auto; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 8px; font-weight: bold; }
        .form-group input { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; }
        .btn-submit { background: var(--primary); color: white; padding: 12px 30px; border: none; border-radius: 5px; cursor: pointer; font-weight: bold; width: 100%; }
        .logo-preview { max-width: 200px; margin-top: 15px; border-radius: 10px; }
    </style>
</head>
<body>
    <div class="form-container">
        <a href="dashboard.php?tab=championships" style="color: var(--primary); text-decoration: none;">← Retour</a>
        <h1>Modifier le logo - <?php echo $championship['name']; ?></h1>
        
        <?php if ($championship['logo_path']): ?>
            <div style="margin-bottom: 20px;">
                <label>Logo actuel:</label>
                <img src="../uploads/<?php echo $championship['logo_path']; ?>" alt="" class="logo-preview">
            </div>
        <?php endif; ?>
        
        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label>Nouveau logo (image)</label>
                <input type="file" name="logo" accept="image/*" required>
            </div>
            <button type="submit" class="btn-submit">Mettre à jour le logo</button>
        </form>
    </div>
</body>
</html>