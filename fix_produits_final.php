<?php
require_once 'config/db.php';

echo "Verification et correction complete:\n";

// Voir la structure actuelle
echo "\nStructure actuelle:\n";
$stmt = $pdo->query("DESCRIBE produits");
while($r = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo $r['Field'] . " - " . $r['Key'] . " - " . $r['Type'] . "\n";
}

echo "\nSuppression des cles primaires multiples...\n";
try { $pdo->exec("ALTER TABLE produits DROP PRIMARY KEY"); echo "OK\n"; } catch(Exception $e) { echo "Erreur: ".$e->getMessage()."\n"; }

echo "\nRenommage id_produit vers id...\n";
try { $pdo->exec("ALTER TABLE produits CHANGE COLUMN id_produit id INT AUTO_INCREMENT PRIMARY KEY"); echo "OK\n"; } catch(Exception $e) { echo "Erreur: ".$e->getMessage()."\n"; }

echo "\nNouvelle structure:\n";
$stmt = $pdo->query("DESCRIBE produits");
while($r = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo $r['Field'] . " - " . $r['Key'] . " - " . $r['Type'] . "\n";
}

