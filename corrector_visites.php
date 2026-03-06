<?php
/**
 * corrector_visites.php - Ajoute la colonne nom_visiteur à la table visites
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<!DOCTYPE html>
<html>
<head>
    <meta charset='utf-8'>
    <title>Correction Table Visites</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f0f0f0; }
        .box { background: white; padding: 20px; border-radius: 10px; max-width: 600px; margin: 20px auto; }
        .success { color: green; background: #d4edda; padding: 10px; border-radius: 5px; margin: 5px 0; }
        .error { color: red; background: #f8d7da; padding: 10px; border-radius: 5px; margin: 5px 0; }
    </style>
</head>
<body>
<div class='box'>
<h1>Correction Table Visites</h1>
";

try {
    require_once 'config/db.php';
    
    // Vérifier si la colonne existe déjà
    $stmt = $pdo->query("DESCRIBE visites");
    $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo "<h3>Colonnes actuelles:</h3>";
    echo "<ul>";
    foreach ($columns as $col) {
        echo "<li>$col</li>";
    }
    echo "</ul>";
    
    // Ajouter la colonne nom_visiteur si elle n'existe pas
    if (!in_array('nom_visiteur', $columns)) {
        try {
            $pdo->exec("ALTER TABLE visites ADD COLUMN nom_visiteur VARCHAR(100) DEFAULT NULL AFTER adresse_ip");
            echo "<p class='success'>✓ Colonne 'nom_visiteur' ajoutée</p>";
        } catch (PDOException $e) {
            echo "<p class='error'>✗ Erreur: " . $e->getMessage() . "</p>";
        }
    } else {
        echo "<p>✓ Colonne 'nom_visiteur' déjà exists</p>";
    }
    
    // Vérifier si utilisateur connecté
    if (isset($_SESSION['user_id']) && isset($_SESSION['user_nom'])) {
        echo "<p class='success'>✓ Utilisateur connecté: " . htmlspecialchars($_SESSION['user_nom']) . "</p>";
    } else {
        echo "<p>ℹ Pas d'utilisateur connecté (visite anonyme)</p>";
    }
    
    echo "<h2 class='success'>Terminé!</h2>";
    
} catch (PDOException $e) {
    echo "<p class='error'>ERREUR: " . $e->getMessage() . "</p>";
}

echo "</div></body></html>";

