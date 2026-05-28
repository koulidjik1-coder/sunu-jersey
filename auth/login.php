<?php
require_once '../config.php';
require_once '../functions.php';

$error = '';
$theme = getActiveTheme($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    if (loginUser($pdo, $_POST['email'], $_POST['password'])) {
        header('Location: ../index.php');
        exit;
    } else {
        $error = 'Email ou mot de passe incorrect';
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - SUNU</title>
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
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="auth-form">
            <h2>🔓 Connexion</h2>
            <?php if ($error): ?>
                <div class="error">⚠️ <?php echo $error; ?></div>
            <?php endif; ?>
            <form method="POST">
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" required>
                </div>
                <div class="form-group">
                    <label>Mot de passe</label>
                    <input type="password" name="password" required>
                </div>
                <button type="submit" name="login" class="btn-auth">Se connecter</button>
            </form>
            <div class="auth-link">
                Pas de compte? <a href="register.php">S'inscrire</a>
            </div>
        </div>
    </div>
</body>
</html>
