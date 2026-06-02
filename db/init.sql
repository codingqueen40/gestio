-- Initial schema for the expense manager
-- Runs automatically on MySQL's first startup

USE gestio;

CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(50) NOT NULL UNIQUE,
    couleur VARCHAR(7) DEFAULT '#6c757d',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS depenses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    libelle VARCHAR(255) NOT NULL,
    montant DECIMAL(10,2) NOT NULL,
    categorie_id INT,
    date_depense DATE NOT NULL,
    note TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (categorie_id) REFERENCES categories(id) ON DELETE SET NULL,
    INDEX idx_date (date_depense),
    INDEX idx_categorie (categorie_id)
);

-- Default categories
INSERT INTO categories (nom, couleur) VALUES
    ('Alimentation', '#28a745'),
    ('Transport', '#007bff'),
    ('Logement', '#dc3545'),
    ('Loisirs', '#ffc107'),
    ('Sante', '#17a2b8'),
    ('Education', '#6610f2'),
    ('Autre', '#6c757d');

-- A few sample expenses (delete these when you start for real)
INSERT INTO depenses (libelle, montant, categorie_id, date_depense) VALUES
    ('Courses Edeka', 87.50, 1, CURDATE()),
    ('Abonnement transport', 49.00, 2, CURDATE() - INTERVAL 2 DAY),
    ('Cinema avec les enfants', 32.00, 4, CURDATE() - INTERVAL 5 DAY);
