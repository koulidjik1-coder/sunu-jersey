<?php
require_once 'config.php';
require_once 'functions.php';

$championships = getChampionships($pdo);
$offers = getActiveOffers($pdo);
$theme = getActiveTheme($pdo);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo SITE_NAME; ?> - <?php echo SITE_TAGLINE; ?></title>
    <link rel="stylesheet" href="styles.css">
    <style>
        :root {
            --primary: <?php echo $theme['color_primary']; ?>;
            --secondary: <?php echo $theme['color_secondary']; ?>;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="container">
            <div class="logo">
                <img src="assets/logo-sunu.png" alt="SUNU JERSEY Logo" class="logo-img">
                <div class="logo-text">
                    <h1>SUNU JERSEY</h1>
                    <p class="tagline"><?php echo SITE_TAGLINE; ?></p>
                </div>
            </div>
            <div class="header-actions">
                <a href="cart.php" class="cart-btn">🛒</a>
                <?php if (isLoggedIn()): ?>
                    <div class="user-menu">
                        <span><?php echo $_SESSION['nom']; ?></span>
                        <?php if (isAdmin()): ?>
                            <a href="admin/dashboard.php">Admin</a>
                        <?php endif; ?>
                        <a href="logout.php">Déconnexion</a>
                    </div>
                <?php else: ?>
                    <a href="auth/login.php" class="btn-login">Connexion</a>
                    <a href="auth/register.php" class="btn-register">Inscription</a>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <!-- Offre du moment (Hero avec background) -->
    <?php if (!empty($offers)): ?>
        <section class="offers-section">
            <div class="offers-carousel">
                <?php foreach ($offers as $index => $offer): ?>
                    <div class="offer-slide" style="display: <?php echo $index === 0 ? 'block' : 'none'; ?>; background-image: url('uploads/<?php echo htmlspecialchars($offer['background_path']); ?>');">
                        <div class="offer-content">
                            <h2>Bienvenue chez SUNU JERSEY</h2>
                            <p><?php echo SITE_TAGLINE; ?></p>
                            <a href="offer.php?id=<?php echo $offer['id']; ?>" class="offer-link">
                                <?php if ($offer['image_path']): ?>
                                    <img src="uploads/<?php echo htmlspecialchars($offer['image_path']); ?>" alt="<?php echo htmlspecialchars($offer['name']); ?>">
                                <?php endif; ?>
                                <span><?php echo htmlspecialchars($offer['name']); ?></span>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <button class="carousel-prev" onclick="prevOffer()">‹</button>
            <button class="carousel-next" onclick="nextOffer()">›</button>
        </section>
    <?php endif; ?>

    <!-- Championnats avec logos -->
    <section class="championships" id="shop">
        <div class="container">
            <h2>Nos Championnats</h2>
            <div class="championships-grid">
                <?php foreach ($championships as $championship): ?>
                    <div class="championship-card">
                        <?php if ($championship['logo_path']): ?>
                            <a href="championship.php?id=<?php echo $championship['id']; ?>" class="championship-logo-link">
                                <img src="uploads/<?php echo htmlspecialchars($championship['logo_path']); ?>" alt="<?php echo htmlspecialchars($championship['name']); ?>" class="championship-logo">
                            </a>
                        <?php else: ?>
                            <h3><?php echo $championship['name']; ?></h3>
                            <a href="championship.php?id=<?php echo $championship['id']; ?>" class="btn-explore">Explorer</a>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <p>&copy; 2026 SUNU JERSEY - <?php echo SITE_TAGLINE; ?></p>
        <p>WhatsApp: 78 861 70 23</p>
    </footer>

    <script>
        let currentOfferIndex = 0;
        const totalOffers = document.querySelectorAll('.offer-slide').length;

        function showOffer(index) {
            const slides = document.querySelectorAll('.offer-slide');
            if (index >= totalOffers) currentOfferIndex = 0;
            if (index < 0) currentOfferIndex = totalOffers - 1;
            slides.forEach(slide => slide.style.display = 'none');
            if (slides[currentOfferIndex]) slides[currentOfferIndex].style.display = 'block';
        }

        function nextOffer() {
            currentOfferIndex++;
            showOffer(currentOfferIndex);
        }

        function prevOffer() {
            currentOfferIndex--;
            showOffer(currentOfferIndex);
        }

        // Auto-change offers every 5 seconds
        if (totalOffers > 1) {
            setInterval(nextOffer, 5000);
        }
    </script>
</body>
</html>