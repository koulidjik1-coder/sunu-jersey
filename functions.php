<?php

function hashPassword($password) {
    return password_hash($password, PASSWORD_BCRYPT);
}

function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isAdmin() {
    return isset($_SESSION['is_admin']) && $_SESSION['is_admin'];
}

function registerUser($pdo, $email, $nom, $telephone, $username, $password) {
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? OR username = ?");
    $stmt->execute([$email, $username]);
    if ($stmt->rowCount() > 0) {
        return false;
    }
    
    $hashed = hashPassword($password);
    $stmt = $pdo->prepare("INSERT INTO users (email, nom, telephone, username, password) VALUES (?, ?, ?, ?, ?)");
    return $stmt->execute([$email, $nom, $telephone, $username, $hashed]);
}

function loginUser($pdo, $email, $password) {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user && verifyPassword($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['nom'] = $user['nom'];
        $_SESSION['is_admin'] = $user['is_admin'];
        return true;
    }
    return false;
}

function getChampionships($pdo) {
    $stmt = $pdo->query("SELECT * FROM championships WHERE is_active = TRUE ORDER BY id");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getCategories($pdo, $championship_id) {
    $stmt = $pdo->prepare("SELECT * FROM categories WHERE championship_id = ? AND is_active = TRUE ORDER BY name");
    $stmt->execute([$championship_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getProducts($pdo, $category_id = null) {
    if ($category_id) {
        $stmt = $pdo->prepare("SELECT DISTINCT products.* FROM products WHERE category_id = ? ORDER BY name");
        $stmt->execute([$category_id]);
    } else {
        $stmt = $pdo->query("SELECT DISTINCT products.* FROM products ORDER BY name");
    }
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getProductById($pdo, $id) {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function getProductImages($pdo, $product_id) {
    $stmt = $pdo->prepare("SELECT * FROM product_images WHERE product_id = ? ORDER BY is_main DESC");
    $stmt->execute([$product_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getActiveTheme($pdo) {
    $stmt = $pdo->query("SELECT * FROM themes WHERE is_active = TRUE LIMIT 1");
    $theme = $stmt->fetch(PDO::FETCH_ASSOC);
    return $theme ?: ['id' => 1, 'name' => 'Ocean Bleu', 'color_primary' => '#0f3460', 'color_secondary' => '#1a5c8f'];
}

function getAllUsers($pdo) {
    $stmt = $pdo->query("SELECT id, email, nom, telephone, username, is_admin, created_at FROM users ORDER BY created_at DESC");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getUserById($pdo, $user_id) {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function makeAdmin($pdo, $user_id) {
    $stmt = $pdo->prepare("UPDATE users SET is_admin = TRUE WHERE id = ?");
    return $stmt->execute([$user_id]);
}

function removeAdmin($pdo, $user_id) {
    $stmt = $pdo->prepare("UPDATE users SET is_admin = FALSE WHERE id = ?");
    return $stmt->execute([$user_id]);
}

function deleteUser($pdo, $user_id) {
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    return $stmt->execute([$user_id]);
}

function setActiveTheme($pdo, $theme_id) {
    $pdo->query("UPDATE themes SET is_active = FALSE");
    $stmt = $pdo->prepare("UPDATE themes SET is_active = TRUE WHERE id = ?");
    return $stmt->execute([$theme_id]);
}

function getAllThemes($pdo) {
    $stmt = $pdo->query("SELECT * FROM themes");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function addChampionship($pdo, $name) {
    $stmt = $pdo->prepare("INSERT INTO championships (name, is_active) VALUES (?, TRUE)");
    return $stmt->execute([$name]);
}

function deleteChampionship($pdo, $championship_id) {
    $stmt = $pdo->prepare("DELETE FROM championships WHERE id = ?");
    return $stmt->execute([$championship_id]);
}

function updateChampionshipLogo($pdo, $championship_id, $logo_path) {
    $stmt = $pdo->prepare("UPDATE championships SET logo_path = ? WHERE id = ?");
    return $stmt->execute([$logo_path, $championship_id]);
}

function addProduct($pdo, $name, $team, $price, $description, $category_id, $championship_id) {
    $stmt = $pdo->prepare("INSERT INTO products (name, team, price, description, category_id, championship_id) VALUES (?, ?, ?, ?, ?, ?)");
    return $stmt->execute([$name, $team, $price, $description, $category_id, $championship_id]);
}

function addProductImage($pdo, $product_id, $image_path, $is_main = false) {
    $stmt = $pdo->prepare("INSERT INTO product_images (product_id, image_path, is_main) VALUES (?, ?, ?)");
    return $stmt->execute([$product_id, $image_path, $is_main ? 1 : 0]);
}

function getActiveOffers($pdo) {
    $stmt = $pdo->query("SELECT * FROM offers WHERE is_active = TRUE ORDER BY created_at DESC");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getOfferById($pdo, $id) {
    $stmt = $pdo->prepare("SELECT * FROM offers WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function addOffer($pdo, $name, $description, $image_path, $background_path, $link_product_id) {
    $stmt = $pdo->prepare("INSERT INTO offers (name, description, image_path, background_path, link_product_id, is_active) VALUES (?, ?, ?, ?, ?, TRUE)");
    return $stmt->execute([$name, $description, $image_path, $background_path, $link_product_id]);
}

function updateOffer($pdo, $id, $name, $description, $image_path, $background_path, $link_product_id) {
    if ($image_path && $background_path) {
        $stmt = $pdo->prepare("UPDATE offers SET name = ?, description = ?, image_path = ?, background_path = ?, link_product_id = ? WHERE id = ?");
        return $stmt->execute([$name, $description, $image_path, $background_path, $link_product_id, $id]);
    } elseif ($image_path) {
        $stmt = $pdo->prepare("UPDATE offers SET name = ?, description = ?, image_path = ?, link_product_id = ? WHERE id = ?");
        return $stmt->execute([$name, $description, $image_path, $link_product_id, $id]);
    } elseif ($background_path) {
        $stmt = $pdo->prepare("UPDATE offers SET name = ?, description = ?, background_path = ?, link_product_id = ? WHERE id = ?");
        return $stmt->execute([$name, $description, $background_path, $link_product_id, $id]);
    } else {
        $stmt = $pdo->prepare("UPDATE offers SET name = ?, description = ?, link_product_id = ? WHERE id = ?");
        return $stmt->execute([$name, $description, $link_product_id, $id]);
    }
}

function deleteOffer($pdo, $id) {
    $stmt = $pdo->prepare("DELETE FROM offers WHERE id = ?");
    return $stmt->execute([$id]);
}

?>