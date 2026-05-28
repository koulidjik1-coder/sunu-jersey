<?php
require_once 'config.php';
require_once 'functions.php';

$championship_id = $_GET['id'] ?? 0;
if (!$championship_id) {
    header('Location: index.php');
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM championships WHERE id = ?");
$stmt->execute([$championship_id]);
$championship = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$championship) {
    header('Location: index.php');
    exit;
}

$categories = getCategories($pdo, $championship_id);
$theme = getActiveTheme($pdo);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $championship['name']; ?> - SUNU</title>
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
        <h1 style="color: var(--primary); margin-bottom: 30px;"><?php echo $championship['name']; ?></h1>
        <div class="categories-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 20px;">
            <?php foreach ($categories as $category): ?>
                <div style="background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); text-align: center;">
                    <h3 style="color: var(--primary); margin-bottom: 15px;"><?php echo $category['name']; ?></h3>
                    <a href="category.php?id=<?php echo $category['id']; ?>" style="display: inline-block; background: var(--primary); color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;">Voir les maillots</a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>