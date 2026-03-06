<?php
/**
 * Script de migration pour ajouter les colonnes manquantes
 * Exécuter une fois pour corriger la structure de la base de données
 */

require_once 'includes/db.php';

echo "<!DOCTYPE html>
<html lang='fr'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Migration Base de Données - SepelasLia</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background: #f5f5f5; padding: 2rem; }
        .container { max-width: 600px; margin: 0 auto; }
        h1 { color: #1a4d2e; }
        .success { background: #d4edda; border: 1px solid #c3e6cb; padding: 1rem; border-radius: 8px; margin: 0.5rem 0; color: #155724; }
        .error { background: #f8d7da; border: 1px solid #f5c6cb; padding: 1rem; border-radius: 8px; margin: 0.5rem 0; color: #721c24; }
        .info { background: #cce5ff; border: 1px solid #b8daff; padding: 1rem; border-radius: 8px; margin: 0.5rem 0; color: #004085; }
        a { display: inline-block; margin-top: 1rem; background: #1a4d2e; color: white; padding: 0.75rem 1.5rem; text-decoration: none; border-radius: 8px; }
        a:hover { background: #143d26; }
    </style>
</head>
<body>
    <div class='container'>
        <h1>🔧 Migration Base de Données</h1>";

$messages = [];
$errors = [];

// 1. Vérifier et ajouter la colonne telephone à utilisateurs
try {
    $stmt = $pdo->query("DESCRIBE utilisateurs");
    $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    if (!in_array('telephone', $columns)) {
        $pdo->exec("ALTER TABLE utilisateurs ADD COLUMN telephone VARCHAR(50) DEFAULT NULL AFTER email");
        $messages[] = "✓ Colonne 'telephone' ajoutée à la table utilisateurs";
    } else {
        $messages[] = "✓ Colonne 'telephone' déjà présente dans utilisateurs";
    }
    
    if (!in_array('adresse_livraison', $columns)) {
        $pdo->exec("ALTER TABLE utilisateurs ADD COLUMN adresse_livraison TEXT DEFAULT NULL AFTER telephone");
        $messages[] = "✓ Colonne 'adresse_livraison' ajoutée à la table utilisateurs";
    } else {
        $messages[] = "✓ Colonne 'adresse_livraison' déjà présente dans utilisateurs";
    }
    
    if (!in_array('date_creation', $columns)) {
        $pdo->exec("ALTER TABLE utilisateurs ADD COLUMN date_creation DATETIME DEFAULT CURRENT_TIMESTAMP");
        $messages[] = "✓ Colonne 'date_creation' ajoutée à la table utilisateurs";
    } else {
        $messages[] = "✓ Colonne 'date_creation' déjà présente dans utilisateurs";
    }
    
} catch (PDOException $e) {
    $errors[] = "Erreur migration utilisateurs: " . $e->getMessage();
}

// 2. Vérifier et corriger la table commandes
try {
    $stmt = $pdo->query("DESCRIBE commandes");
    $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    $required_cols = [
        'telephone_client' => 'VARCHAR(50)',
        'adresse_livraison' => 'TEXT',
        'numero_commande' => 'VARCHAR(50)'
    ];
    
    foreach ($required_cols as $col => $type) {
        if (!in_array($col, $columns)) {
            if ($col === 'telephone_client') {
                $pdo->exec("ALTER TABLE commandes ADD COLUMN telephone_client VARCHAR(50) DEFAULT NULL AFTER nom_client");
            } elseif ($col === 'adresse_livraison') {
                $pdo->exec("ALTER TABLE commandes ADD COLUMN adresse_livraison TEXT DEFAULT NULL AFTER telephone_client");
            } elseif ($col === 'numero_commande') {
                $pdo->exec("ALTER TABLE commandes ADD COLUMN numero_commande VARCHAR(50) DEFAULT NULL AFTER id_commande");
            }
            $messages[] = "✓ Colonne '$col' ajoutée à la table commandes";
        } else {
            $messages[] = "✓ Colonne '$col' déjà présente dans commandes";
        }
    }
    
} catch (PDOException $e) {
    $errors[] = "Erreur migration commandes: " . $e->getMessage();
}

// 3. Vérifier et créer la table points_de_vente
try {
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS points_de_vente (
            id_pdv INT AUTO_INCREMENT PRIMARY KEY,
            nom_pdv VARCHAR(255) NOT NULL,
            adresse_pdv VARCHAR(500),
            ville VARCHAR(100) DEFAULT 'Brazzaville',
            telephone_pdv VARCHAR(50),
            horaires VARCHAR(100),
            actif TINYINT(1) DEFAULT 1,
            date_creation DATETIME DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ");
    $messages[] = "✓ Table points_de_vente créée/vérifiée";
} catch (PDOException $e) {
    $errors[] = "Erreur points_de_vente: " . $e->getMessage();
}

// 4. Insérer les points de vente par défaut
try {
    $stmt = $pdo->query("SELECT COUNT(*) FROM points_de_vente");
    $count = $stmt->fetchColumn();
    
    if ($count == 0) {
        $pdv_defaults = [
            ['Maman CLEMENTINE', 'Centre-ville, Rue principale', 'Brazzaville', '+242 06 612 34 56', '7h-19h'],
            ['SUPER ALIMENTATION', 'Quartier Plateau', 'Brazzaville', '+242 06 723 45 67', '8h-20h'],
            ['SAJA MARKET', 'Ouenzé, Avenue des Martyrs', 'Brazzaville', '+242 06 834 56 78', '8h-21h'],
            ['BEIRUT MARKET', 'Moungali, Rue de la Palestine', 'Brazzaville', '+242 06 945 67 89', '9h-19h']
        ];
        
        $stmt = $pdo->prepare("INSERT INTO points_de_vente (nom_pdv, adresse_pdv, ville, telephone_pdv, horaires, actif) VALUES (?, ?, ?, ?, ?, 1)");
        
        foreach ($pdv_defaults as $pdv) {
            $stmt->execute($pdv);
        }
        
        $messages[] = "✓ " . count($pdv_defaults) . " points de vente ajoutés";
    }
} catch (PDOException $e) {
    $errors[] = "Erreur insertion points de vente: " . $e->getMessage();
}

// Afficher les résultats
foreach ($messages as $msg) {
    echo "<div class='success'>$msg</div>";
}

foreach ($errors as $err) {
    echo "<div class='error'>$err</div>";
}

if (empty($errors)) {
    echo "<div class='info'>✅ Migration terminée avec succès! Toutes les colonnes requises sont maintenant présentes.</div>";
}

echo "<a href='index.php'>🏠 Retour au site</a>";
echo "</div></body></html>";
?>

