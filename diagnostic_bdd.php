<?php
require 'includes/db.php';

echo "=== DIAGNOSTIC BDD ===\n\n";

// Tables
$tables = ['produits', 'categories', 'commandes', 'utilisateurs', 'points_de_vente'];

foreach ($tables as $table) {
    try {
        $stmt = $pdo->query("DESCRIBE $table");
        echo "TABLE: $table\n";
        foreach ($stmt->fetchAll() as $col) {
            echo "  - " . $col['Field'] . " (" . $col['Type'] . ")\n";
        }
        echo "\n";
    } catch (Exception $e) {
        echo "❌ TABLE $table N'EXISTE PAS!\n\n";
    }
}

// Vérifier quelques records
echo "=== DONNÉES ===\n\n";

try {
    $count = $pdo->query("SELECT COUNT(*) FROM produits")->fetchColumn();
    echo "Produits: $count\n";

    $count = $pdo->query("SELECT COUNT(*) FROM categories")->fetchColumn();
    echo "Catégories: $count\n";

    $count = $pdo->query("SELECT COUNT(*) FROM points_de_vente")->fetchColumn();
    echo "Points de vente: $count\n";
} catch (Exception $e) {
    echo "Erreur: " . $e->getMessage() . "\n";
}
?>
