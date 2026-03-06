<?php
/**
 * install_tables.php - Installation des tables pour Images Accueil et Catalogues
 * Exécuter ce fichier une fois pour créer les tables
 */

require_once 'config/db.php';

echo "<h1>Installation des tables</h1>";

try {
    // Créer la table images_accueil
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS images_accueil (
            id_image INT AUTO_INCREMENT PRIMARY KEY,
            titre VARCHAR(255) DEFAULT NULL,
            description TEXT DEFAULT NULL,
            image VARCHAR(255) NOT NULL,
            lien VARCHAR(255) DEFAULT NULL,
            position INT DEFAULT 0,
            actif TINYINT(1) DEFAULT 1,
            date_ajout TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
    ");
    echo "<p style='color: green;'>✓ Table 'images_accueil' créée avec succès!</p>";
    
    // Créer la table catalogues
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS catalogues (
            id_catalogue INT AUTO_INCREMENT PRIMARY KEY,
            titre VARCHAR(255) NOT NULL,
            description TEXT DEFAULT NULL,
            fichier VARCHAR(255) NOT NULL,
            type_fichier ENUM('pdf', 'image') DEFAULT 'pdf',
            actif TINYINT(1) DEFAULT 1,
            date_ajout TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
    ");
    echo "<p style='color: green;'>✓ Table 'catalogues' créée avec succès!</p>";
    
    echo "<h2 style='color: green;'>Installation terminée!</h2>";
    echo "<p>Vous pouvez maintenant:</p>";
    echo "<ul>";
    echo "<li>Aller dans Admin > Images Accueil pour ajouter des bannières</li>";
    echo "<li>Aller dans Admin > Catalogues pour ajouter des catalogues</li>";
    echo "<li>Les utilisateurs peuvent voir les catalogues depuis le menu</li>";
    echo "</ul>";
    echo "<p><a href='index.php'>Retour à l'accueil</a></p>";
    
} catch (PDOException $e) {
    echo "<p style='color: red;'>Erreur: " . $e->getMessage() . "</p>";
}
?>
<style>
body { font-family: Arial, sans-serif; max-width: 600px; margin: 50px auto; padding: 20px; }
h1 { color: #333; }
a { color: #2563eb; }
</style>

