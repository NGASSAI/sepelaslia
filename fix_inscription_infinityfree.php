<?php
/**
 * fix_inscription_infinityfree.php - Correction complète pour InfinityFree
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<!DOCTYPE html>
<html>
<head>
    <meta charset='utf-8'>
    <title>Correction Inscription - InfinityFree</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
        .box { background: white; padding: 25px; border-radius: 12px; max-width: 700px; margin: 20px auto; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { color: #1a4d2e; margin-top: 0; }
        .success { color: #155724; background: #d4edda; padding: 12px; border-radius: 6px; margin: 8px 0; border-left: 4px solid #28a745; }
        .error { color: #721c24; background: #f8d7da; padding: 12px; border-radius: 6px; margin: 8px 0; border-left: 4px solid #dc3545; }
        .info { color: #0c5460; background: #d1ecf1; padding: 12px; border-radius: 6px; margin: 8px 0; border-left: 4px solid #17a2b8; }
        table { width: 100%; border-collapse: collapse; margin: 15px 0; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #f9f9f9; }
    </style>
</head>
<body>
<div class='box'>
<h1>🔧 Correction Inscription - InfinityFree</h1>
";

try {
    // Auto-détection InfinityFree (plusieurs extensions)
    $http_host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '';
    $is_infinityfree = (
        strpos($http_host, 'infinityfree') !== false ||
        strpos($http_host, 'epizy.com') !== false ||
        strpos($http_host, 'ct.ws') !== false ||
        strpos($http_host, 'freevar.com') !== false ||
        strpos($http_host, '000webhost') !== false
    );
    
    echo "<p class='info'>Serveur détecté: " . ($is_infinityfree ? 'InfinityFree (Production)' : 'Local (Développement)') . "</p>";
    
    // Configuration InfinityFree
    if ($is_infinityfree) {
        $host = 'sql309.infinityfree.com';
        $user = 'if0_41240795';
        $pass = 'sepelaSlia1234';
        $db = 'if0_41240795_sepelaslia';
    } else {
        $host = 'localhost';
        $user = 'root';
        $pass = '';
        $db = 'sepelasLia';
    }
    
    // Connexion
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    
    echo "<p class='success'>✓ Connexion BDD réussie</p>";
    
    // 1. Voir structure actuelle
    echo "<h2>1. Structure actuelle de la table utilisateurs:</h2>";
    $stmt = $pdo->query("DESCRIBE utilisateurs");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<table><tr><th>Colonne</th><th>Type</th></tr>";
    foreach ($columns as $col) {
        echo "<tr><td>" . $col['Field'] . "</td><td>" . $col['Type'] . "</td></tr>";
    }
    echo "</table>";
    
    // 2. Colonnes nécessaires
    $needed = [
        'id_user' => 'INT PRIMARY KEY AUTO_INCREMENT',
        'nom_complet' => 'VARCHAR(150)',
        'email' => 'VARCHAR(100)',
        'mot_de_passe' => 'VARCHAR(255)',
        'telephone' => 'VARCHAR(20)',
        'role' => "ENUM('admin','client') DEFAULT 'client'",
        'date_inscription' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP',
        'date_creation' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP'
    ];
    
    echo "<h2>2. Ajout des colonnes manquantes:</h2>";
    
    $existing_cols = array_column($columns, 'Field');
    
    foreach ($needed as $col_name => $col_def) {
        if (!in_array($col_name, $existing_cols)) {
            try {
                // Essayer différents types
                $types_to_try = [
                    $col_def,
                    str_replace('VARCHAR(150)', 'VARCHAR(255)', $col_def),
                    str_replace('VARCHAR(100)', 'VARCHAR(150)', $col_def)
                ];
                
                $added = false;
                foreach ($types_to_try as $type) {
                    try {
                        $pdo->exec("ALTER TABLE utilisateurs ADD COLUMN $col_name $type");
                        echo "<p class='success'>✓ Colonne '$col_name' ajoutée ($type)</p>";
                        $added = true;
                        break;
                    } catch (PDOException $e2) {
                        // Essayer un autre type
                    }
                }
                
                if (!$added) {
                    echo "<p class='error'>✗ Impossible d'ajouter '$col_name'</p>";
                }
            } catch (PDOException $e) {
                echo "<p class='error'>✗ Erreur pour '$col_name': " . substr($e->getMessage(), 0, 100) . "</p>";
            }
        } else {
            echo "<p class='info'>✓ Colonne '$col_name' existe déjà</p>";
        }
    }
    
    // 3. Vérification finale
    echo "<h2>3. Vérification finale:</h2>";
    $stmt = $pdo->query("DESCRIBE utilisateurs");
    $final_cols = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo "<p>Colonnes actuelles: " . implode(', ', $final_cols) . "</p>";
    
    // 4. Tester insertion
    echo "<h2>4. Test d'insertion:</h2>";
    try {
        $test_email = 'test_' . time() . '@test.com';
        $stmt = $pdo->prepare("INSERT INTO utilisateurs (nom_complet, email, mot_de_passe, telephone, role) VALUES (?, ?, ?, ?, 'client')");
        $stmt->execute(['Test User', $test_email, password_hash('test123', PASSWORD_DEFAULT), '+242000000000']);
        
        // Supprimer le test
        $pdo->exec("DELETE FROM utilisateurs WHERE email = '$test_email'");
        
        echo "<p class='success'>✓ Test d'insertion réussi! L'inscription devrait maintenant fonctionner.</p>";
    } catch (PDOException $e) {
        echo "<p class='error'>✗ Erreur lors du test: " . $e->getMessage() . "</p>";
    }
    
    echo "<h2 style='color:green;'>🎉 Terminé!</h2>";
    echo "<p>L'inscription devrait maintenant fonctionner sur InfinityFree.</p>";
    echo "<p><a href='register.php'>Aller à la page d'inscription</a></p>";
    
} catch (PDOException $e) {
    echo "<p class='error'>ERREUR: " . $e->getMessage() . "</p>";
}

echo "</div></body></html>";

