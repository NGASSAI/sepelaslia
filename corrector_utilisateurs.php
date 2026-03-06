<?php
/**
 * corrector_utilisateurs.php - Corrige la table utilisateurs
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<!DOCTYPE html>
<html>
<head>
    <meta charset='utf-8'>
    <title>Correction Table Utilisateurs</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f0f0f0; }
        .box { background: white; padding: 20px; border-radius: 10px; max-width: 600px; margin: 20px auto; }
        .success { color: green; background: #d4edda; padding: 10px; border-radius: 5px; margin: 5px 0; }
        .error { color: red; background: #f8d7da; padding: 10px; border-radius: 5px; margin: 5px 0; }
    </style>
</head>
<body>
<div class='box'>
<h1>Correction Table Utilisateurs</h1>
";

try {
    require_once 'config/db.php';
    
    echo "<p>Connexion OK</p>";
    
    // Voir la structure actuelle
    echo "<h3>Structure actuelle de la table utilisateurs:</h3>";
    $stmt = $pdo->query("DESCRIBE utilisateurs");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<ul>";
    foreach ($columns as $col) {
        echo "<li>" . $col['Field'] . " - " . $col['Type'] . "</li>";
    }
    echo "</ul>";
    
    // Colonnes attendues
    $needed_cols = [
        'nom_complet' => 'VARCHAR(100)',
        'email' => 'VARCHAR(150)',
        'telephone' => 'VARCHAR(20)',
        'mot_de_passe' => 'VARCHAR(255)',
        'role' => "ENUM('admin','client') DEFAULT 'client'",
        'date_inscription' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP',
        'active' => 'TINYINT(1) DEFAULT 1'
    ];
    
    echo "<h3>Ajout des colonnes manquantes:</h3>";
    
    foreach ($needed_cols as $col_name => $col_type) {
        $exists = false;
        foreach ($columns as $col) {
            if ($col['Field'] === $col_name) {
                $exists = true;
                break;
            }
        }
        
        if (!$exists) {
            try {
                $pdo->exec("ALTER TABLE utilisateurs ADD COLUMN $col_name $col_type");
                echo "<p class='success'>✓ Colonne '$col_name' ajoutée</p>";
            } catch (PDOException $e) {
                echo "<p class='error'>✗ Erreur ajout '$col_name': " . $e->getMessage() . "</p>";
            }
        } else {
            echo "<p>✓ Colonne '$col_name' déjà exists</p>";
        }
    }
    
    echo "<h3>Vérification finale:</h3>";
    $stmt = $pdo->query("DESCRIBE utilisateurs");
    $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "<p>Colonnes actuelles: " . implode(', ', $columns) . "</p>";
    
    echo "<h2 class='success'>Terminé!</h2>";
    echo "<p>La table utilisateurs a été corrigée. Vous pouvez maintenant tester l'inscription.</p>";
    
} catch (PDOException $e) {
    echo "<p class='error'>ERREUR: " . $e->getMessage() . "</p>";
}

echo "</div></body></html>";

