<?php
/**
 * Script de correction de la base de données
 * Ajoute la colonne id_pdv à la table commandes si elle n'existe pas
 */

require_once 'includes/db.php';

echo "<!DOCTYPE html>
<html lang='fr'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Correction Base de Données - SepelasLia</title>
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
        <h1>🔧 Correction Base de Données</h1>";

$messages = [];
$errors = [];

// 1. Vérifier et ajouter id_pdv à commandes
try {
    $stmt = $pdo->query("DESCRIBE commandes");
    $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    if (!in_array('id_pdv', $columns)) {
        // Essayer d'abord de renommer point_de_vente_id vers id_pdv
        if (in_array('point_de_vente_id', $columns)) {
            $pdo->exec("ALTER TABLE commandes CHANGE COLUMN point_de_vente_id id_pdv INT DEFAULT NULL");
            $messages[] = "✓ Colonne 'point_de_vente_id' renommée en 'id_pdv'";
        } else {
            // Créer la colonne
            $pdo->exec("ALTER TABLE commandes ADD COLUMN id_pdv INT DEFAULT NULL AFTER id_user");
            $messages[] = "✓ Colonne 'id_pdv' ajoutée à la table commandes";
        }
    } else {
        $messages[] = "✓ Colonne 'id_pdv' déjà présente dans commandes";
    }
    
} catch (PDOException $e) {
    $errors[] = "Erreur commandes: " . $e->getMessage();
}

// 2. Vérifier details_commande pour sous_total
try {
    $stmt = $pdo->query("DESCRIBE details_commande");
    $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    if (!in_array('sous_total', $columns)) {
        $pdo->exec("ALTER TABLE details_commande ADD COLUMN sous_total DECIMAL(10,2) NOT NULL AFTER prix_unitaire");
        $messages[] = "✓ Colonne 'sous_total' ajoutée à details_commande";
    } else {
        $messages[] = "✓ Colonne 'sous_total' déjà présente dans details_commande";
    }
    
} catch (PDOException $e) {
    $errors[] = "Erreur details_commande: " . $e->getMessage();
}

// Afficher les résultats
foreach ($messages as $msg) {
    echo "<div class='success'>$msg</div>";
}

foreach ($errors as $err) {
    echo "<div class='error'>$err</div>";
}

if (empty($errors)) {
    echo "<div class='info'>✅ Corrections terminées avec succès!</div>";
}

echo "<a href='index.php'>🏠 Retour au site</a>";
echo "</div></body></html>";
?>

