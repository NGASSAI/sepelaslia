<?php
require_once 'config/db.php';
echo "Correction id_produit...\n";

try {
    $pdo->exec("ALTER TABLE produits DROP PRIMARY KEY");
    echo "OK: Cle primaire supprimee\n";
} catch(Exception $e) { echo "Erreur: ".$e->getMessage()."\n"; }

try {
    $pdo->exec("ALTER TABLE produits CHANGE COLUMN id_produit id INT AUTO_INCREMENT PRIMARY KEY");
    echo "OK: id_produit -> id\n";
} catch(Exception $e) { echo "Erreur: ".$e->getMessage()."\n"; }

$cols = $pdo->query("DESCRIBE produits")->fetchAll(PDO::FETCH_COLUMN);
echo "Resultat: ".implode(", ", $cols)."\n";

