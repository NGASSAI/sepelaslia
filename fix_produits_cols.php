<?php
require_once 'config/db.php';
echo "Correction des colonnes produits...\n";

try {
    $pdo->exec("ALTER TABLE produits CHANGE COLUMN id_produit id INT AUTO_INCREMENT PRIMARY KEY");
    echo "OK: id_produit -> id\n";
} catch(Exception $e) { echo "Erreur: ".$e->getMessage()."\n"; }

try {
    $pdo->exec("ALTER TABLE produits CHANGE COLUMN nom_produit nom VARCHAR(150) NOT NULL");
    echo "OK: nom_produit -> nom\n";
} catch(Exception $e) { echo "Erreur: ".$e->getMessage()."\n"; }

try {
    $pdo->exec("ALTER TABLE produits CHANGE COLUMN description_prod description TEXT");
    echo "OK: description_prod -> description\n";
} catch(Exception $e) { echo "Erreur: ".$e->getMessage()."\n"; }

try {
    $pdo->exec("ALTER TABLE produits CHANGE COLUMN stock_quantite stock INT DEFAULT 0");
    echo "OK: stock_quantite -> stock\n";
} catch(Exception $e) { echo "Erreur: ".$e->getMessage()."\n"; }

try {
    $pdo->exec("ALTER TABLE produits CHANGE COLUMN image_prod image VARCHAR(255)");
    echo "OK: image_prod -> image\n";
} catch(Exception $e) { echo "Erreur: ".$e->getMessage()."\n"; }

echo "\nVerifications:\n";
$cols = $pdo->query("DESCRIBE produits")->fetchAll(PDO::FETCH_COLUMN);
echo implode(", ", $cols)."\n";
echo "Termine!\n";

