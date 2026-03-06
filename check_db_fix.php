<?php
/**
 * Script de vérification et correction de la base de données
 */

try {
    require_once 'config/db.php';
    
    echo "=== Vérification de la table commandes ===\n\n";
    
    // Vérifier les colonnes existantes
    $stmt = $pdo->query("DESCRIBE commandes");
    $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo "Colonnes actuelles:\n";
    foreach ($columns as $col) {
        echo "  - $col\n";
    }
    
    echo "\n";
    
    // Vérifier si numero_commande existe
    if (!in_array('numero_commande', $columns)) {
        echo "❌ Colonne 'numero_commande' ABSENTE - Ajout en cours...\n";
        $pdo->exec("ALTER TABLE commandes ADD COLUMN numero_commande VARCHAR(50) NULL AFTER id");
        echo "✓ Colonne 'numero_commande' ajoutée avec succès!\n";
    } else {
        echo "✓ Colonne 'numero_commande' déjà présente\n";
    }
    
    // Vérifier les autres colonnes utiles
    $useful_cols = [
        'nom_client' => 'VARCHAR(100)',
        'telephone_client' => 'VARCHAR(20)',
        'email_client' => 'VARCHAR(150)',
        'adresse_livraison' => 'TEXT',
        'date_creation' => 'TIMESTAMP NULL'
    ];
    
    foreach ($useful_cols as $col => $type) {
        if (!in_array($col, $columns)) {
            echo "❌ Colonne '$col' ABSENTE - Ajout en cours...\n";
            $pdo->exec("ALTER TABLE commandes ADD COLUMN $col $type");
            echo "✓ Colonne '$col' ajoutée avec succès!\n";
        } else {
            echo "✓ Colonne '$col' déjà présente\n";
        }
    }
    
    echo "\n=== Vérification terminée ===\n";
    
} catch (PDOException $e) {
    echo "Erreur: " . $e->getMessage() . "\n";
}
