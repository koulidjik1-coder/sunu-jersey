<?php
require_once '../config.php';
require_once '../functions.php';

if (!isAdmin()) {
    header('Location: ../index.php');
    exit;
}

$users = getAllUsers($pdo);
$championships = getChampionships($pdo);
$themes = getAllThemes($pdo);
$offers = $pdo->query("SELECT o.*, p.name as product_name FROM offers o LEFT JOIN products p ON o.link_product_id = p.id")->fetchAll(PDO::FETCH_ASSOC);
$active_tab = $_GET['tab'] ?? 'users';
$theme = getActiveTheme($pdo);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - SUNU</title>
    <link rel="stylesheet" href="../styles.css">
    <style>
        :root {
            --primary: <?php echo $theme['color_primary']; ?>;
            --secondary: <?php echo $theme['color_secondary']; ?>;
        }
        .admin-container { max-width: 1400px; margin: 0 auto; padding: 20px; }
        .admin-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .admin-header h1 { color: var(--primary); }
        .btn-back { background: #999; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; }
        .admin-tabs { display: flex; gap: 10px; margin-bottom: 30px; border-bottom: 2px solid #ddd; flex-wrap: wrap; }
        .admin-tabs a { padding: 12px 20px; text-decoration: none; color: #333; cursor: pointer; transition: all 0.3s; }
        .admin-tabs a.active { border-bottom: 3px solid var(--primary); color: var(--primary); font-weight: bold; }
        .admin-content { background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .users-table { width: 100%; border-collapse: collapse; }
        .users-table th, .users-table td { padding: 15px; text-align: left; border-bottom: 1px solid #ddd; }
        .users-table th { background: #f5f5f5; font-weight: bold; color: var(--primary); }
        .btn-admin, .btn-delete { padding: 8px 12px; border: none; border-radius: 5px; cursor: pointer; font-size: 12px; margin-right: 5px; transition: all 0.3s; }
        .btn-admin { background: #3498db; color: white; }
        .btn-admin:hover { background: #2980b9; }
        .btn-delete { background: #e74c3c; color: white; }
        .btn-delete:hover { background: #c0392b; }
        .themes-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 20px; }
        .theme-card { padding: 20px; border-radius: 10px; text-align: center; cursor: pointer; border: 3px solid transparent; transition: all 0.3s; }
        .theme-card:hover { transform: translateY(-5px); }
        .theme-card.active { border: 3px solid var(--primary); box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
        .theme-card h3 { color: white; margin: 0; }
        .championship-section { margin-bottom: 40px; }
        .championship-form { background: #f5f5f5; padding: 20px; border-radius: 5px; margin-bottom: 20px; }
        .championship-form input { padding: 10px; border: 1px solid #ddd; border-radius: 5px; margin-right: 10px; }
        .championship-form button { padding: 10px 20px; background: var(--primary); color: white; border: none; border-radius: 5px; cursor: pointer; }
        .championship-item { background: #f0f0f0; padding: 15px; border-radius: 5px; margin-bottom: 10px; display: flex; justify-content: space-between; align-items: center; }
        .offer-item { background: white; border: 1px solid #ddd; padding: 15px; border-radius: 5px; margin-bottom: 15px; }
    </style>
</head>
<body>
    <div class="admin-container">
        <div class="admin-header">
            <h1>⚙️ Admin Dashboard</h1>
            <a href="../index.php" class="btn-back">← Retour au site</a>
        </div>

        <div class="admin-tabs">
            <a href="?tab=users" class="<?php echo $active_tab === 'users' ? 'active' : ''; ?>">👥 Utilisateurs</a>
            <a href="?tab=themes" class="<?php echo $active_tab === 'themes' ? 'active' : ''; ?>">🎨 Thèmes</a>
            <a href="?tab=championships" class="<?php echo $active_tab === 'championships' ? 'active' : ''; ?>">🏆 Championnats</a>
            <a href="?tab=offers" class="<?php echo $active_tab === 'offers' ? 'active' : ''; ?>">🎁 Offres</a>
            <a href="?tab=products" class="<?php echo $active_tab === 'products' ? 'active' : ''; ?>">👕 Maillots</a>
        </div>

        <div class="admin-content">
            <?php if ($active_tab === 'users'): ?>
                <h2>Gestion des utilisateurs</h2>
                <table class="users-table">
                    <tr>
                        <th>Email</th>
                        <th>Nom</th>
                        <th>Téléphone</th>
                        <th>Username</th>
                        <th>Admin</th>
                        <th>Inscrit le</th>
                        <th>Actions</th>
                    </tr>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?php echo $user['email']; ?></td>
                            <td><?php echo $user['nom']; ?></td>
                            <td><?php echo $user['telephone'] ?? '-'; ?></td>
                            <td><?php echo $user['username']; ?></td>
                            <td><?php echo $user['is_admin'] ? '✅ Oui' : '❌ Non'; ?></td>
                            <td><?php echo date('d/m/Y', strtotime($user['created_at'])); ?></td>
                            <td>
                                <?php if ($user['email'] !== 'koulidjik@gmail.com'): ?>
                                    <?php if (!$user['is_admin']): ?>
                                        <a href="manage_user.php?action=make_admin&id=<?php echo $user['id']; ?>" class="btn-admin">Make Admin</a>
                                    <?php else: ?>
                                        <a href="manage_user.php?action=remove_admin&id=<?php echo $user['id']; ?>" class="btn-admin">Remove Admin</a>
                                    <?php endif; ?>
                                    <a href="manage_user.php?action=delete&id=<?php echo $user['id']; ?>" class="btn-delete" onclick="return confirm('Êtes-vous sûr?')">Supprimer</a>
                                <?php else: ?>
                                    <span style="color: #666;">Admin Principal</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </table>

            <?php elseif ($active_tab === 'themes'): ?>
                <h2>🎨 Changer le thème</h2>
                <div class="themes-grid">
                    <?php foreach ($themes as $t): ?>
                        <div class="theme-card <?php echo $t['is_active'] ? 'active' : ''; ?>" style="background: linear-gradient(135deg, <?php echo $t['color_primary']; ?> 0%, <?php echo $t['color_secondary']; ?> 100%);">
                            <h3><?php echo $t['name']; ?></h3>
                            <p style="color: white; margin: 10px 0; font-size: 12px;">
                                <?php echo $t['color_primary']; ?> - <?php echo $t['color_secondary']; ?>
                            </p>
                            <?php if (!$t['is_active']): ?>
                                <a href="manage_theme.php?id=<?php echo $t['id']; ?>" style="color: white; text-decoration: none; font-weight: bold; display: inline-block; padding: 8px 16px; background: rgba(255,255,255,0.2); border-radius: 3px; margin-top: 10px;">Activer</a>
                            <?php else: ?>
                                <span style="color: white; font-weight: bold; display: inline-block; padding: 8px 16px; background: rgba(0,0,0,0.2); border-radius: 3px; margin-top: 10px;">✅ Actif</span>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>

            <?php elseif ($active_tab === 'championships'): ?>
                <h2>🏆 Gérer les championnats</h2>
                <div class="championship-section">
                    <h3>Ajouter un championnat</h3>
                    <div class="championship-form">
                        <form method="POST" action="manage_championship.php" style="display: flex; gap: 10px;">
                            <input type="text" name="name" placeholder="Nom du championnat" required style="flex: 1;">
                            <button type="submit" name="add">+ Ajouter</button>
                        </form>
                    </div>
                </div>

                <h3>Championnats existants</h3>
                <?php foreach ($championships as $champ): ?>
                    <div class="championship-item">
                        <div>
                            <strong><?php echo $champ['name']; ?></strong>
                            <?php if ($champ['logo_path']): ?>
                                <p style="margin: 5px 0; font-size: 12px; color: #666;">Logo: ✅</p>
                            <?php endif; ?>
                        </div>
                        <div>
                            <a href="edit_championship.php?id=<?php echo $champ['id']; ?>" style="background: #3498db; color: white; padding: 8px 12px; text-decoration: none; border-radius: 5px; margin-right: 10px;">Modifier Logo</a>
                            <a href="manage_championship.php?action=delete&id=<?php echo $champ['id']; ?>" class="btn-delete" onclick="return confirm('Supprimer?')">Supprimer</a>
                        </div>
                    </div>
                <?php endforeach; ?>

            <?php elseif ($active_tab === 'offers'): ?>
                <h2>🎁 Offres du moment</h2>
                <p><a href="add_offer.php" style="display: inline-block; padding: 12px 30px; background: var(--primary); color: white; text-decoration: none; border-radius: 5px; margin-bottom: 20px;">+ Ajouter une offre</a></p>
                
                <?php foreach ($offers as $offer): ?>
                    <div class="offer-item">
                        <div>
                            <h4><?php echo htmlspecialchars($offer['name']); ?></h4>
                            <p style="margin: 5px 0; color: #666; font-size: 14px;"><?php echo $offer['product_name'] ? 'Lié à: ' . $offer['product_name'] : 'Non lié'; ?></p>
                        </div>
                        <div>
                            <a href="edit_offer.php?id=<?php echo $offer['id']; ?>" style="background: #3498db; color: white; padding: 8px 12px; text-decoration: none; border-radius: 5px; margin-right: 10px;">Modifier</a>
                            <a href="delete_offer.php?id=<?php echo $offer['id']; ?>" class="btn-delete" onclick="return confirm('Supprimer?')">Supprimer</a>
                        </div>
                    </div>
                <?php endforeach; ?>

            <?php elseif ($active_tab === 'products'): ?>
                <h2>👕 Ajouter des maillots</h2>
                <p><a href="add_product.php" style="display: inline-block; padding: 12px 30px; background: var(--primary); color: white; text-decoration: none; border-radius: 5px; margin-bottom: 20px;">+ Ajouter un maillot</a></p>
                
                <h3>Maillots disponibles</h3>
                <?php
                $stmt = $pdo->query("SELECT p.*, c.name as category_name, ch.name as championship_name FROM products p JOIN categories c ON p.category_id = c.id JOIN championships ch ON p.championship_id = ch.id ORDER BY ch.name, c.name");
                $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
                ?>
                <table class="users-table">
                    <tr>
                        <th>Maillot</th>
                        <th>Équipe</th>
                        <th>Catégorie</th>
                        <th>Championnat</th>
                        <th>Prix</th>
                        <th>Actions</th>
                    </tr>
                    <?php foreach ($products as $prod): ?>
                        <tr>
                            <td><?php echo $prod['name']; ?></td>
                            <td><?php echo $prod['team']; ?></td>
                            <td><?php echo $prod['category_name']; ?></td>
                            <td><?php echo $prod['championship_name']; ?></td>
                            <td><?php echo number_format($prod['price'], 2); ?>€</td>
                            <td>
                                <a href="delete_product.php?id=<?php echo $prod['id']; ?>" class="btn-delete" onclick="return confirm('Supprimer?')">Supprimer</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>