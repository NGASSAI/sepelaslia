<?php
/**
 * migrate_complete.php - Script de migration COMPLET et CORRECTIF
 * Exécuter via: http://localhost/sepelasLia/migrate_complete.php
 * 
 * Ce script corrige:
 * - Table messages (colonne lu)
 * - Table notifications
 * - Table commandes (statistiques)
 * - Toutes les colonnes manquantes
 */
require_once 'includes/db.php';

echo "<!DOCTYPE html>
<html><head>
<meta charset='utf-8'>
<meta name='viewport' content='width=device-width, initial-scale=1'>
<style>
body { font-family: -apple-system, sans-serif; padding: 20px; background: #f5f5f5; }
h1 { color: #1a4d2e; }
h2 { color: #c4652a; margin-top: 30px; }
.success { color: #22c55e; }
.error { color: #ef4444; }
.box { background: white; padding: 20px; border-radius: 10px; margin: 10px 0; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
table { width: 100%; border-collapse: collapse; margin: 10px 0; }
td, th { padding: 8px; border: 1px solid #ddd; text-align: left; }
a { color: #1a4d2e; }
</style>
</head><body>";

echo "<h1>=== Migration Complete - SepelasLia ===</h1>";

$errors = [];
$success = [];

try {
    // ========================
    // 1. TABLE MESSAGES
    // ========================
    echo "<h2>1. Table messages</h2>";
    
    // Vérifier si la table existe
    $stmt = $pdo->query("SHOW TABLES LIKE 'messages'");
    if ($stmt->rowCount() === 0) {
        $pdo->exec("
            CREATE TABLE messages (
                id_message INT AUTO_INCREMENT PRIMARY KEY,
                nom VARCHAR(100) NOT NULL,
                email VARCHAR(150) NOT NULL,
                message TEXT NOT NULL,
                reponse TEXT DEFAULT NULL,
                lu TINYINT(1) DEFAULT 0,
                date_envoi DATETIME DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");
        $success[] = "Table 'messages' créée";
    } else {
        $success[] = "Table 'messages' existe";
    }
    
    // Ajouter colonne lu si absente
    $stmt = $pdo->query("SHOW COLUMNS FROM messages LIKE 'lu'");
    if ($stmt->fetch() === false) {
        $pdo->exec("ALTER TABLE messages ADD COLUMN lu TINYINT(1) DEFAULT 0");
        $success[] = "Colonne 'lu' ajoutée à messages";
    } else {
        $success[] = "Colonne 'lu' existe dans messages";
    }
    
    // Ajouter colonne reponse si absente
    $stmt = $pdo->query("SHOW COLUMNS FROM messages LIKE 'reponse'");
    if ($stmt->fetch() === false) {
        $pdo->exec("ALTER TABLE messages ADD COLUMN reponse TEXT DEFAULT NULL");
        $success[] = "Colonne 'reponse' ajoutée à messages";
    }
    
    // ========================
    // 2. TABLE NOTIFICATIONS
    // ========================
    echo "<h2>2. Table notifications</h2>";
    
    $stmt = $pdo->query("SHOW TABLES LIKE 'notifications'");
    if ($stmt->rowCount() === 0) {
        $pdo->exec("
            CREATE TABLE notifications (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                type VARCHAR(50) DEFAULT 'commande',
                titre VARCHAR(255) NOT NULL,
                message TEXT NOT NULL,
                lu TINYINT(1) DEFAULT 0,
                date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_user (user_id),
                INDEX idx_lu (lu)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");
        $success[] = "Table 'notifications' créée";
    } else {
        $success[] = "Table 'notifications' existe";
    }
    
    // Vérifier colonnes
    $required_notif_cols = ['user_id', 'type', 'titre', 'message', 'lu', 'date_creation'];
    foreach ($required_notif_cols as $col) {
        $stmt = $pdo->query("SHOW COLUMNS FROM notifications LIKE '$col'");
        if ($stmt->fetch() === false) {
            if ($col === 'user_id') $pdo->exec("ALTER TABLE notifications ADD COLUMN $col INT NOT NULL");
            elseif ($col === 'type') $pdo->exec("ALTER TABLE notifications ADD COLUMN $col VARCHAR(50) DEFAULT 'commande'");
            elseif ($col === 'titre') $pdo->exec("ALTER TABLE notifications ADD COLUMN $col VARCHAR(255) NOT NULL");
            elseif ($col === 'message') $pdo->exec("ALTER TABLE notifications ADD COLUMN $col TEXT NOT NULL");
            elseif ($col === 'lu') $pdo->exec("ALTER TABLE notifications ADD COLUMN $col TINYINT(1) DEFAULT 0");
            elseif ($col === 'date_creation') $pdo->exec("ALTER TABLE notifications ADD COLUMN $col DATETIME DEFAULT CURRENT_TIMESTAMP");
            $success[] = "Colonne '$col' ajoutée à notifications";
        }
    }
    
    // ========================
    // 3. TABLE COMMANDES
    // ========================
    echo "<h2>3. Table commandes</h2>";
    
    $required_cmd_cols = [
        'numero_commande' => 'VARCHAR(50) NULL',
        'statut' => "ENUM('en_attente','confirmee','retrait','completee','annulee') DEFAULT 'en_attente'",
        'total_montant' => 'DECIMAL(10,2) DEFAULT 0',
        'date_creation' => 'DATETIME DEFAULT CURRENT_TIMESTAMP',
        'email_client' => 'VARCHAR(150) DEFAULT NULL',
        'telephone_client' => 'VARCHAR(50) DEFAULT NULL'
    ];
    
    foreach ($required_cmd_cols as $col => $def) {
        $stmt = $pdo->query("SHOW COLUMNS FROM commandes LIKE '$col'");
        if ($stmt->fetch() === false) {
            $pdo->exec("ALTER TABLE commandes ADD COLUMN $col $def");
            $success[] = "Colonne '$col' ajoutée à commandes";
        } else {
            $success[] = "Colonne '$col' existe dans commandes";
        }
    }
    
    // Mettre à jour les anciennes commandes sans numero_commande
    $pdo->exec("UPDATE commandes SET numero_commande = CONCAT('CMD-', id, '-', DATE_FORMAT(COALESCE(date_creation, NOW()), '%Y%m%d')) WHERE numero_commande IS NULL OR numero_commande = ''");
    
    // ========================
    // 4. VÉRIFICATION FINALE
    // ========================
    echo "<h2>4. Vérification</h2>";
    
    // Compter les données
    $cmd_count = $pdo->query("SELECT COUNT(*) FROM commandes")->fetchColumn();
    $msg_count = $pdo->query("SELECT COUNT(*) FROM messages")->fetchColumn();
    $notif_count = $pdo->query("SELECT COUNT(*) FROM notifications")->fetchColumn();
    
    echo "<div class='box'>";
    echo "<p><strong>Commandes:</strong> $cmd_count</p>";
    echo "<p><strong>Messages:</strong> $msg_count</p>";
    echo "<p><strong>Notifications:</strong> $notif_count</p>";
    echo "</div>";
    
    // Statistiques commandes
    $stats = $pdo->query("
        SELECT statut, COUNT(*) as count, COALESCE(SUM(total_montant), 0) as total
        FROM commandes 
        GROUP BY statut
    ")->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<div class='box'>";
    echo "<h3>Statistiques commandes:</h3>";
    echo "<table><tr><th>Statut</th><th>Nombre</th><th>Total (FCFA)</th></tr>";
    $total_all = 0;
    $total_count = 0;
    foreach ($stats as $s) {
        $total_all += $s['total'];
        $total_count += $s['count'];
        echo "<tr><td>{$s['statut']}</td><td>{$s['count']}</td><td>" . number_format($s['total'], 0, ',', ' ') . "</td></tr>";
    }
    echo "<tr><td><strong>TOTAL</strong></td><td><strong>$total_count</strong></td><td><strong>" . number_format($total_all, 0, ',', ' ') . " FCFA</strong></td></tr>";
    echo "</table>";
    echo "</div>";
    
    echo "<h2 style='color:green'>✓ Migration terminée avec succès!</h2>";
    echo "<p><a href='admin/commandes.php'>Aller au panel admin - Commandes</a></p>";
    echo "<p><a href='admin/messages.php'>Aller au panel admin - Messages</a></p>";
    
} catch (PDOException $e) {
    echo "<h2 style='color:red'>Erreur: " . $e->getMessage() . "</h2>";
}

echo "</body></html>";
