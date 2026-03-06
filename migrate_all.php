ter name="content"><?php
/**
 * migrate_all.php - Script de migration complet
 * À exécuter via le navigateur: http://localhost/sepelasLia/migrate_all.php
 */
require_once 'includes/db.php';

echo "<h1>=== Migration de la base de données ===</h1>";

try {
    // 1. Table commandes - ajouter numero_commande si absent
    echo "<h3>1. Table commandes</h3>";
    $stmt = $pdo->query("SHOW COLUMNS FROM commandes LIKE 'numero_commande'");
    if ($stmt->fetch() === false) {
        $pdo->exec("ALTER TABLE commandes ADD COLUMN numero_commande VARCHAR(50) NULL");
        echo "✓ Colonne 'numero_commande' ajoutée<br>";
    } else {
        echo "✓ Colonne 'numero_commande' existe déjà<br>";
    }
    
    // 2. Table messages - ajouter created_at si absent
    echo "<h3>2. Table messages</h3>";
    $stmt = $pdo->query("SHOW COLUMNS FROM messages LIKE 'created_at'");
    if ($stmt->fetch() === false) {
        $pdo->exec("ALTER TABLE messages ADD COLUMN created_at DATETIME DEFAULT CURRENT_TIMESTAMP");
        echo "✓ Colonne 'created_at' ajoutée<br>";
    } else {
        echo "✓ Colonne 'created_at' existe déjà<br>";
    }
    
    echo "<h2 style='color:green'>✓ Toutes les migrations terminées avec succès!</h2>";
    echo "<p><a href='admin/commandes.php'>Retour à la gestion des commandes</a></p>";
    
} catch (PDOException $e) {
    echo "<h2 style='color:red'>Erreur: " . $e->getMessage() . "</h2>";
}
