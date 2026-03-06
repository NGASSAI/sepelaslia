<?php
require_once 'config/db.php';

echo "Recreation table produits avec les bonnes colonnes...\n";

// Recuperer les donnees existantes
echo "1. Sauvegarde des donnees...\n";
$donnees = $pdo->query("SELECT * FROM produits")->fetchAll(PDO::FETCH_ASSOC);
echo "   " . count($donnees) . " produits trouves\n";

// Supprimer la table
echo "2. Suppression de l'ancienne table...\n";
$pdo->exec("DROP TABLE IF EXISTS produits");

// Creer la nouvelle table
echo "3. Creation nouvelle table...\n";
$pdo->exec("CREATE TABLE produits (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(150) NOT NULL,
    description TEXT,
    prix DECIMAL(10,0) NOT NULL,
    image VARCHAR(255),
    stock INT DEFAULT 0,
    unite VARCHAR(20) DEFAULT 'piece',
    categorie VARCHAR(50) DEFAULT 'produit',
    date_peremption DATE NULL,
    frais_shipping DECIMAL(10,0) DEFAULT 0,
    actif TINYINT(1) DEFAULT 1,
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

echo "4. Table produits corrigee!\n";

// Verifier
$cols = $pdo->query("DESCRIBE produits")->fetchAll(PDO::FETCH_COLUMN);
echo "Colonnes: " . implode(", ", $cols) . "\n";
echo "Termine!\n";

