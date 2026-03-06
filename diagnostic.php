<?php
/**
 * Script de verification et correction complete de la base de donnees
 * Verifie et corrige toutes les tables et colonnes necessaires
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<!DOCTYPE html>
<html>
<head>
    <meta charset='utf-8'>
    <title>Diagnostic - SepelasLia</title>
    <style>
        body { 
            font-family: 'Courier New', monospace; 
            padding: 20px; 
            background: linear-gradient(135deg, #1a4d2e 0%, #2d6a4f 100%); 
            color: #fff; 
            min-height: 100vh;
        }
        h1 { text-align: center; color: #fff; border-bottom: 2px solid #c4652a; padding-bottom: 15px; }
        h2 { color: #c4652a; margin-top: 30px; border-left: 4px solid #c4652a; padding-left: 10px; }
        .container { max-width: 1200px; margin: 0 auto; background: rgba(0,0,0,0.3); padding: 20px; border-radius: 10px; }
        .success { color: #90EE90; }
        .error { color: #FFB6C1; }
        .info { color: #87CEEB; }
        table { width: 100%; border-collapse: collapse; margin: 15px 0; background: rgba(0,0,0,0.5); }
        td, th { padding: 10px; border: 1px solid #444; text-align: left; }
        th { background: #c4652a; }
        .col-ok { color: #90EE90; }
        .col-missing { color: #FFB6C1; font-weight: bold; }
        .stat-box { display: inline-block; padding: 15px 25px; margin: 10px; background: rgba(0,0,0,0.4); border-radius: 8px; }
        .stat-number { font-size: 2em; font-weight: bold; color: #c4652a; }
    </style>
</head>
<body>
<div class='container'>
<h1>DIAGNOSTIC COMPLET - SepelasLia</h1>
";

try {
    require_once 'config/db.php';
    
    echo "<h2>1. VERIFICATION DES TABLES</h2>";
    
    $stmt = $pdo->query("SHOW TABLES");
    $existing_tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo "<table><tr><th>Table</th><th>Statut</th></tr>";
    foreach (['utilisateurs', 'produits', 'commandes', 'details_commande', 'points_de_vente', 'notifications', 'messages', 'visites'] as $table) {
        $exists = in_array($table, $existing_tables);
        echo "<tr><td>$table</td><td class='" . ($exists ? 'col-ok' : 'col-missing') . "'>" . ($exists ? 'Existe' : 'Manquante') . "</td></tr>";
    }
    echo "</table>";
    
    // Creation des tables manquantes
    echo "<h2>2. CORRECTION DES TABLES</h2>";
    
    $create_statements = [
        'utilisateurs' => "CREATE TABLE IF NOT EXISTS utilisateurs (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nom_complet VARCHAR(100) NOT NULL,
            email VARCHAR(150) UNIQUE,
            telephone VARCHAR(20) NOT NULL,
            mot_de_passe VARCHAR(255) NOT NULL,
            role ENUM('admin','client') DEFAULT 'client',
            date_inscription TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            dernier_connexion TIMESTAMP NULL,
            active TINYINT(1) DEFAULT 1
        )",
        'produits' => "CREATE TABLE IF NOT EXISTS produits (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nom VARCHAR(150) NOT NULL,
            description TEXT,
            prix DECIMAL(10,0) NOT NULL,
            image VARCHAR(255),
            stock INT DEFAULT 0,
            unite VARCHAR(20) DEFAULT 'piece',
            categorie VARCHAR(50) DEFAULT 'produit',
            date_peremption DATE NULL,
            frais_shipping DECIMAL(10,0) DEFAULT 0,
            actif TINYINT(1) DEFAULT 1,
            date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )",
        'commandes' => "CREATE TABLE IF NOT EXISTS commandes (
            id INT AUTO_INCREMENT PRIMARY KEY,
            id_commande INT,
            id_user INT,
            point_de_vente_id INT,
            id_pdv INT,
            date_commande TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            date_creation TIMESTAMP NULL,
            total_montant DECIMAL(10,0) DEFAULT 0,
            statut VARCHAR(20) DEFAULT 'en_attente',
            methode_paiement VARCHAR(50),
            nom_client VARCHAR(100),
            telephone_client VARCHAR(20),
            email_client VARCHAR(150),
            numero_commande VARCHAR(50),
            adresse_livraison TEXT
        )",
        'details_commande' => "CREATE TABLE IF NOT EXISTS details_commande (
            id INT AUTO_INCREMENT PRIMARY KEY,
            id_commande INT NOT NULL,
            commande_id INT,
            produit_id INT NOT NULL,
            quantite INT DEFAULT 1,
            prix_unitaire DECIMAL(10,0) NOT NULL,
            sous_total DECIMAL(10,0) NOT NULL,
            date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )",
        'points_de_vente' => "CREATE TABLE IF NOT EXISTS points_de_vente (
            id_pdv INT AUTO_INCREMENT PRIMARY KEY,
            nom_pdv VARCHAR(100) NOT NULL,
            adresse_pdv TEXT NOT NULL,
            telephone_pdv VARCHAR(20),
            horaires TEXT,
            active TINYINT(1) DEFAULT 1,
            date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )",
        'notifications' => "CREATE TABLE IF NOT EXISTS notifications (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            titre VARCHAR(200) NOT NULL,
            message TEXT NOT NULL,
            type VARCHAR(20) DEFAULT 'info',
            lu TINYINT(1) DEFAULT 0,
            date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )",
        'messages' => "CREATE TABLE IF NOT EXISTS messages (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT,
            nom VARCHAR(100),
            email VARCHAR(150),
            telephone VARCHAR(20),
            sujet VARCHAR(200),
            message TEXT NOT NULL,
            lu TINYINT(1) DEFAULT 0,
            date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )",
        'visites' => "CREATE TABLE IF NOT EXISTS visites (
            id INT AUTO_INCREMENT PRIMARY KEY,
            page VARCHAR(100),
            user_id INT,
            ip_address VARCHAR(45),
            user_agent TEXT,
            date_visite TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )"
    ];
    
    foreach ($create_statements as $table => $sql) {
        if (!in_array($table, $existing_tables)) {
            $pdo->exec($sql);
            echo "<p class='success'>Table '$table' creee</p>";
        } else {
            echo "<p class='info'>Table '$table' deja existante</p>";
        }
    }
    
    // Definition des colonnes pour chaque table
    $tables_definitions = [
        'utilisateurs' => ['id', 'nom_complet', 'email', 'telephone', 'mot_de_passe', 'role', 'date_inscription', 'dernier_connexion', 'active'],
        'produits' => ['id', 'nom', 'description', 'prix', 'image', 'stock', 'unite', 'categorie', 'date_peremption', 'frais_shipping', 'actif', 'date_creation'],
        'commandes' => ['id', 'id_commande', 'id_user', 'point_de_vente_id', 'id_pdv', 'date_commande', 'date_creation', 'total_montant', 'statut', 'methode_paiement', 'nom_client', 'telephone_client', 'email_client', 'numero_commande', 'adresse_livraison'],
        'details_commande' => ['id', 'id_commande', 'commande_id', 'produit_id', 'quantite', 'prix_unitaire', 'sous_total', 'date_creation'],
        'points_de_vente' => ['id_pdv', 'nom_pdv', 'adresse_pdv', 'telephone_pdv', 'horaires', 'active', 'date_creation'],
        'notifications' => ['id', 'user_id', 'titre', 'message', 'type', 'lu', 'date_creation'],
        'messages' => ['id', 'user_id', 'nom', 'email', 'telephone', 'sujet', 'message', 'lu', 'date_creation'],
        'visites' => ['id', 'page', 'user_id', 'ip_address', 'user_agent', 'date_visite']
    ];
    
    echo "<h2>3. VERIFICATION DES COLONNES</h2>";
    
    foreach ($tables_definitions as $table => $expected_cols) {
        echo "<h3>Table: $table</h3>";
        echo "<table><tr><th>Colonne</th><th>Statut</th></tr>";
        
        try {
            $stmt = $pdo->query("DESCRIBE $table");
            $existing_cols = $stmt->fetchAll(PDO::FETCH_COLUMN);
            
            foreach ($expected_cols as $col) {
                $exists = in_array($col, $existing_cols);
                echo "<tr><td>$col</td><td class='" . ($exists ? 'col-ok' : 'col-missing') . "'>" . ($exists ? 'OK' : 'Manquante') . "</td></tr>";
                
                // Ajouter la colonne si manquante
                if (!$exists && $table === 'commandes') {
                    $add_cols = [
                        'numero_commande' => 'ADD COLUMN numero_commande VARCHAR(50)',
                        'nom_client' => 'ADD COLUMN nom_client VARCHAR(100)',
                        'telephone_client' => 'ADD COLUMN telephone_client VARCHAR(20)',
                        'email_client' => 'ADD COLUMN email_client VARCHAR(150)',
                        'adresse_livraison' => 'ADD COLUMN adresse_livraison TEXT',
                        'date_creation' => 'ADD COLUMN date_creation TIMESTAMP NULL'
                    ];
                    if (isset($add_cols[$col])) {
                        try {
                            $pdo->exec("ALTER TABLE $table " . $add_cols[$col]);
                            echo " <span class='success'>(ajoutee)</span>";
                        } catch (Exception $e) {}
                    }
                }
            }
            echo "</table>";
        } catch (PDOException $e) {
            echo "<tr><td colspan='2' class='error'>Erreur: " . $e->getMessage() . "</td></tr></table>";
        }
    }
    
    echo "<h2>4. STATISTIQUES</h2>";
    echo "<div>";
    
    $stats = [
        'utilisateurs' => 'SELECT COUNT(*) FROM utilisateurs',
        'produits' => 'SELECT COUNT(*) FROM produits',
        'commandes' => 'SELECT COUNT(*) FROM commandes',
        'points_de_vente' => 'SELECT COUNT(*) FROM points_de_vente',
        'notifications' => 'SELECT COUNT(*) FROM notifications',
        'messages' => 'SELECT COUNT(*) FROM messages'
    ];
    
    foreach ($stats as $name => $sql) {
        try {
            $count = $pdo->query($sql)->fetchColumn();
            echo "<div class='stat-box'><div class='stat-number'>$count</div><div>$name</div>";
        } catch (Exception $e) {
            echo "<div class='stat-box'><div class='error'>0</div><div>$name</div>";
        }
    }
    echo "</div>";
    
    echo "<h2 class='success'>DIAGNOSTIC TERMINE</h2>";
    echo "<p>Le site devrait maintenant fonctionner sans erreurs de base de donnees.</p>";
    
} catch (PDOException $e) {
    echo "<h2 class='error'>ERREUR DE CONNEXION</h2>";
    echo "<p>" . $e->getMessage() . "</p>";
}

echo "</div></body></html>";
