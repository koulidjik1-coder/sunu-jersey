<?php
require_once 'config.php';
require_once 'functions.php';

$error = '';
$theme = getActiveTheme($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    if (registerUser($pdo, $_POST['email'], $_POST['nom'], $_POST['telephone'], $_POST['username'], $_POST['password'])) {
        $success = true;
    } else {
        $error = 'Email ou nom d\'utilisateur déjà utilisé';
    }
}

$success = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    if (registerUser($pdo, $_POST['email'], $_POST['nom'], $_POST['telephone'] ?? '', $_POST['username'], $_POST['password'])) {
        $success = true;
    } else {
        $error = 'Email ou nom d\'utilisateur déjà utilisé';
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription - SUNU</title>
    <link rel="stylesheet" href="../styles.css">
    <style>
        :root {
            --primary: <?php echo $theme['color_primary']; ?>;
            --secondary: <?php echo $theme['color_secondary']; ?>;
        }
        body { background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%); min-height: 100vh; }
        .auth-container { max-width: 400px; margin: 80px auto; }
        .auth-form { background: white; padding: 40px; border-radius: 10px; box-shadow: 0 10px 30px rgba(0,0,0,0.2); }
        .auth-form h2 { text-align: center; margin-bottom: 30px; color: var(--primary); }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 8px; font-weight: 600; }
        .form-group input { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; }
        .form-group input:focus { outline: none; border-color: var(--primary); box-shadow: 0 0 5px var(--primary); }
        .btn-auth { width: 100%; padding: 12px; background: var(--primary); color: white; border: none; border-radius: 5px; cursor: pointer; font-weight: bold; transition: all 0.3s; }
        .btn-auth:hover { background: var(--secondary); }
        .auth-link { text-align: center; margin-top: 20px; }
        .auth-link a { color: var(--primary); text-decoration: none; }
        .error { color: #e74c3c; background: #fdeae8; padding: 12px; border-radius: 5px; margin-bottom: 20px; border-left: 4px solid #e74c3c; }
        .success { color: #27ae60; background: #e8f8f5; padding: 12px; border-radius: 5px; margin-bottom: 20px; border-left: 4px solid #27ae60; }
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="auth-form">
            <h2>👤 Inscription</h2>
            <?php if ($success): ?>
                <div class="success">✅ Inscription réussie! <br><a href="login.php">Se connecter</a></div>
            <?php endif; ?>
            <?php if ($error): ?>
                <div class="error">⚠️ <?php echo $error; ?></div>
            <?php endif; ?>
            <?php if (!$success): ?>
                <form method="POST">
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label>Nom complet</label>
                        <input type="text" name="nom" required>
                    </div>
                    <div class="form-group">
                        <label>Numéro de téléphone</label>
                        <input type="tel" name="telephone" placeholder="+212XXXXXXXXX">
                    </div>
                    <div class="form-group">
                        <label>Nom d'utilisateur</label>
                        <input type="text" name="username" required>
                    </div>
                    <div class="form-group">
                        <label>Mot de passe</label>
                        <input type="password" name="password" required>
                    </div>
                    <button type="submit" name="register" class="btn-auth">S'inscrire</button>
                </form>
            <?php endif; ?>
            <div class="auth-link">
                Déjà inscrit? <a href="login.php">Se connecter</a>
            </div>
        </div>
    </div>
</body>
</html>