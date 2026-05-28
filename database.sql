CREATE DATABASE IF NOT EXISTS sunu_jersey;
USE sunu_jersey;

-- Table utilisateurs
CREATE TABLE IF NOT EXISTS users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR(255) UNIQUE NOT NULL,
    nom VARCHAR(255) NOT NULL,
    telephone VARCHAR(20),
    username VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    is_admin BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table thèmes
CREATE TABLE IF NOT EXISTS themes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    color_primary VARCHAR(7),
    color_secondary VARCHAR(7),
    is_active BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table championnats
CREATE TABLE IF NOT EXISTS championships (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    logo_path VARCHAR(255),
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table catégories (équipes)
CREATE TABLE IF NOT EXISTS categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    championship_id INT NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (championship_id) REFERENCES championships(id) ON DELETE CASCADE
);

-- Table produits (maillots)
CREATE TABLE IF NOT EXISTS products (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    team VARCHAR(255) NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    description TEXT,
    category_id INT NOT NULL,
    championship_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id),
    FOREIGN KEY (championship_id) REFERENCES championships(id)
);

-- Table images produits
CREATE TABLE IF NOT EXISTS product_images (
    id INT PRIMARY KEY AUTO_INCREMENT,
    product_id INT NOT NULL,
    image_path VARCHAR(255) NOT NULL,
    is_main BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- Table offres du moment
CREATE TABLE IF NOT EXISTS offers (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    image_path VARCHAR(255),
    background_path VARCHAR(255),
    link_product_id INT,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (link_product_id) REFERENCES products(id)
);

-- Table commandes
CREATE TABLE IF NOT EXISTS orders (
    id INT PRIMARY KEY AUTO_INCREMENT,
    order_number VARCHAR(50) UNIQUE NOT NULL,
    user_id INT,
    total_quantity INT NOT NULL,
    total_price DECIMAL(10, 2) NOT NULL,
    status ENUM('pending', 'whatsapp', 'completed') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Table articles commande
CREATE TABLE IF NOT EXISTS order_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id)
);

-- Insérer l'admin
INSERT INTO users (email, nom, telephone, username, password, is_admin) VALUES 
('koulidjik@gmail.com', 'Mouhamed Rassoul', '+212788617023', 'Rasspucha', '$2y$10$M.2cE7q5QzJ6p6L5QzJ6p6L5QzJ6p6L5QzJ6p6L5QzJ6p6L5QzJ6', TRUE);

-- Insérer les thèmes
INSERT INTO themes (name, color_primary, color_secondary, is_active) VALUES 
('Ocean Bleu', '#0f3460', '#1a5c8f', TRUE),
('Dark Mode', '#1a1a1a', '#333333', FALSE),
('Gold Premium', '#d4af37', '#9d7d1c', FALSE);

-- Insérer les 5 grands championnats
INSERT INTO championships (name, is_active) VALUES 
('Premier League', TRUE),
('La Liga', TRUE),
('Ligue 1', TRUE),
('Serie A', TRUE),
('Bundesliga', TRUE);

-- Insérer les catégories (équipes)
INSERT INTO categories (name, championship_id, is_active) VALUES 
('Arsenal', 1, TRUE),
('Manchester United', 1, TRUE),
('Liverpool', 1, TRUE),
('Chelsea', 1, TRUE),
('Real Madrid', 2, TRUE),
('Barcelona', 2, TRUE),
('Atletico Madrid', 2, TRUE),
('Sevilla', 2, TRUE),
('PSG', 3, TRUE),
('Marseille', 3, TRUE),
('Lyon', 3, TRUE),
('Lille', 3, TRUE),
('Juventus', 4, TRUE),
('AC Milan', 4, TRUE),
('Inter Milan', 4, TRUE),
('AS Roma', 4, TRUE),
('Bayern Munich', 5, TRUE),
('Borussia Dortmund', 5, TRUE),
('Bayer Leverkusen', 5, TRUE),
('RB Leipzig', 5, TRUE);