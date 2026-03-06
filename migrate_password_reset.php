<?php
/**
 * Migration: Ajouter les colonnes pour la réinitialisation de mot de passe
 * Exécuter ce fichier UNE SEULE FOIS pour ajouter les colonnes à la table utilisateurs
 */

$page_title = 'Migration - Réinitialisation mot de passe';

require_once 'config/db.php';

try {
    // Vérifier si les colonnes existent déjà
    $stmt = $pdo->query("DESCRIBE utilisateurs");
    $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    $results = [];
    
    // Ajouter reset_token si inexistant
    if (!in_array('reset_token', $columns)) {
        $pdo->exec("ALTER TABLE utilisateurs ADD COLUMN reset_token VARCHAR(64) NULL");
        $results[] = "✓ Colonne 'reset_token' ajoutée";
    } else {
        $results[] = "○ Colonne 'reset_token' existe déjà";
    }
    
    // Ajouter reset_token_expiry si inexistant
    if (!in_array('reset_token_expiry', $columns)) {
        $pdo->exec("ALTER TABLE utilisateurs ADD COLUMN reset_token_expiry DATETIME NULL");
        $results[] = "✓ Colonne 'reset_token_expiry' ajoutée";
    } else {
        $results[] = "○ Colonne 'reset_token_expiry' existe déjà";
    }
    
    // Créer index pour les requêtes de reset (ignorer si déjà existant)
    try {
        $pdo->exec("ALTER TABLE utilisateurs ADD INDEX idx_reset_token (reset_token)");
        $results[] = "✓ Index créé";
    } catch (PDOException $e) {
        if (strpos($e->getMessage(), 'Duplicate') !== false) {
            $results[] = "○ Index existe déjà";
        } else {
            throw $e;
        }
    }
    
    echo "<h1>Migration terminée</h1>";
    echo "<ul>";
    foreach ($results as $result) {
        echo "<li>" . htmlspecialchars($result) . "</li>";
    }
    echo "</ul>";
    echo "<p><a href='index.php'>Retour à l'accueil</a></p>";
    
} catch (PDOException $e) {
    echo "<h1>Erreur de migration</h1>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
}
?>

