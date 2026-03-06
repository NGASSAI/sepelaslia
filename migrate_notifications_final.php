<?php
/**
 * migrate_notifications_final.php - Migration complète des notifications
 * Exécuter via: http://localhost/sepelasLia/migrate_notifications_final.php
 */
require_once 'includes/db.php';

echo "<h1>=== Migration Notifications ===</h1>";

try {
    // 1. Vérifier si la table notifications existe
    $stmt = $pdo->query("SHOW TABLES LIKE 'notifications'");
    if ($stmt->rowCount() === 0) {
        // Créer la table
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS notifications (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                type VARCHAR(50) DEFAULT 'commande',
                titre VARCHAR(255) NOT NULL,
                message TEXT NOT NULL,
                lu TINYINT(1) DEFAULT 0,
                date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_user_id (user_id),
                INDEX idx_lu (lu),
                INDEX idx_date (date_creation)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");
        echo "✓ Table 'notifications' créée<br>";
    } else {
        echo "✓ Table 'notifications' existe déjà<br>";
    }
    
    // 2. Vérifier les colonnes
    $stmt = $pdo->query("SHOW COLUMNS FROM notifications");
    $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    if (!in_array('date_creation', $columns)) {
        $pdo->exec("ALTER TABLE notifications ADD COLUMN date_creation DATETIME DEFAULT CURRENT_TIMESTAMP AFTER lu");
        echo "✓ Colonne 'date_creation' ajoutée<br>";
    } else {
        echo "✓ Colonne 'date_creation' existe<br>";
    }
    
    if (!in_array('type', $columns)) {
        $pdo->exec("ALTER TABLE notifications ADD COLUMN type VARCHAR(50) DEFAULT 'commande' AFTER user_id");
        echo "✓ Colonne 'type' ajoutée<br>";
    } else {
        echo "✓ Colonne 'type' existe<br>";
    }
    
    // 3. Vérifier numero_commande dans commandes
    $stmt = $pdo->query("SHOW COLUMNS FROM commandes LIKE 'numero_commande'");
    if ($stmt->fetch() === false) {
        $pdo->exec("ALTER TABLE commandes ADD COLUMN numero_commande VARCHAR(50) NULL");
        echo "✓ Colonne 'numero_commande' ajoutée à commandes<br>";
    }
    
    // 4. Vérifier created_at dans messages
    $stmt = $pdo->query("SHOW COLUMNS FROM messages LIKE 'created_at'");
    if ($stmt->fetch() === false) {
        $pdo->exec("ALTER TABLE messages ADD COLUMN created_at DATETIME DEFAULT CURRENT_TIMESTAMP");
        echo "✓ Colonne 'created_at' ajoutée à messages<br>";
    }
    
    echo "<h2 style='color:green'>✓ Migration terminée!</h2>";
    echo "<p><a href='admin/commandes.php'>Aller au panel admin</a></p>";
    
} catch (PDOException $e) {
    echo "<h2 style='color:red'>Erreur: " . $e->getMessage() . "</h2>";
}
