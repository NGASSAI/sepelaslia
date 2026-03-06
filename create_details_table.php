<?php
require 'includes/db.php';

$sql = "
CREATE TABLE IF NOT EXISTS details_commande (
    id_detail INT PRIMARY KEY AUTO_INCREMENT,
    id_commande INT NOT NULL,
    id_produit INT NOT NULL,
    quantite INT NOT NULL DEFAULT 1,
    prix_unitaire DECIMAL(10,2) NOT NULL,
    sous_total DECIMAL(10,2) NOT NULL,
    date_ajout TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_commande) REFERENCES commandes(id_commande),
    FOREIGN KEY (id_produit) REFERENCES produits(id_produit)
)";

try {
    $pdo->exec($sql);
    echo "✅ Table details_commande créée ou existe déjà\n";
} catch (Exception $e) {
    if (strpos($e->getMessage(), 'already exists') !== false) {
        echo "ℹ️  Table details_commande existe déjà\n";
    } else {
        echo "❌ Erreur: " . $e->getMessage() . "\n";
    }
}
?>
