<?php
/**
 * Script de migration pour ajouter la colonne numero_commande à la table commandes
 * À exécuter depuis le navigateur: http://localhost/sepelasLia/migrate_numero_commande.php
 */

// Connexion directe à la base de données
$host = 'localhost';
$dbname = 'sepelasLia';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h2>=== MIGRATION COMMANDES ===</h2>";
    
    // Vérifier si la colonne email_client existe
    $stmt = $pdo->query("SHOW COLUMNS FROM commandes LIKE 'email_client'");
    if ($stmt->fetch() === false) {
        $pdo->exec("ALTER TABLE commandes ADD COLUMN email_client VARCHAR(255) NULL AFTER telephone_client");
        echo "<p>✓ Colonne email_client ajoutée</p>";
    } else {
        echo "<p>✓ Colonne email_client existe déjà</p>";
    }
    
    // Vérifier si la colonne numero_commande existe
    $stmt = $pdo->query("SHOW COLUMNS FROM commandes LIKE 'numero_commande'");
    if ($stmt->fetch() === false) {
        $pdo->exec("ALTER TABLE commandes ADD COLUMN numero_commande VARCHAR(50) NULL AFTER email_client");
        echo "<p>✓ Colonne numero_commande ajoutée</p>";
    } else {
        echo "<p>✓ Colonne numero_commande existe déjà</p>";
    }
    
    // Vérifier si la colonne date_creation existe
    $stmt = $pdo->query("SHOW COLUMNS FROM commandes LIKE 'date_creation'");
    if ($stmt->fetch() === false) {
        $pdo->exec("ALTER TABLE commandes ADD COLUMN date_creation DATETIME NULL AFTER date_commande");
        echo "<p>✓ Colonne date_creation ajoutée</p>";
    } else {
        echo "<p>✓ Colonne date_creation existe déjà</p>";
    }
    
    // Mettre à jour les anciennes commandes sans numero_commande
    $stmt = $pdo->query("SELECT id_commande, date_commande FROM commandes WHERE numero_commande IS NULL OR numero_commande = ''");
    $commandes = $stmt->fetchAll();
    
    if (count($commandes) > 0) {
        echo "<p>Mise à jour des " . count($commandes) . " commandes existantes...</p>";
        foreach ($commandes as $cmd) {
            $dateCmd = $cmd['date_commande'] ?? date('Y-m-d H:i:s');
            $numero = 'CMD-' . date('Ymd', strtotime($dateCmd)) . '-' . str_pad($cmd['id_commande'], 4, '0', STR_PAD_LEFT);
            $update = $pdo->prepare("UPDATE commandes SET numero_commande = ? WHERE id_commande = ?");
            $update->execute([$numero, $cmd['id_commande']]);
        }
        echo "<p>✓ Commandes mises à jour avec numéros</p>";
    } else {
        echo "<p>✓ Toutes les commandes ont déjà un numéro</p>";
    }
    
    echo "<h3 style='color:green;'>=== MIGRATION TERMINÉE AVEC SUCCÈS ===</h3>";
    echo "<p><a href='index.php'>Retour à l'accueil</a></p>";
    
} catch (PDOException $e) {
    echo "<p style='color:red;'>❌ Erreur: " . $e->getMessage() . "</p>";
}
?>

