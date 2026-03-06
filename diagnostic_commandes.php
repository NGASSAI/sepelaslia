<?php
/**
 * Script de diagnostic pour admin/commandes.php
 */
$host = 'localhost';
$dbname = 'sepelasLia';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h2>=== DIAGNOSTIC COMMANDES ADMIN ===</h2>";
    
    // Test 1: Vérifier la structure des tables
    echo "<h3>1. Structure de la table commandes:</h3>";
    $stmt = $pdo->query("DESCRIBE commandes");
    $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "<p>Colonnes: " . implode(', ', $columns) . "</p>";
    
    // Test 2: Vérifier s'il y a des commandes
    echo "<h3>2. Nombre de commandes:</h3>";
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM commandes");
    $total = $stmt->fetch();
    echo "<p>Total commandes: " . $total['total'] . "</p>";
    
    // Test 3: Requête utilisée dans admin/commandes.php
    echo "<h3>3. Test de la requête admin:</h3>";
    try {
        $stmt = $pdo->query("
            SELECT c.*, 
                   COUNT(dc.id) as nb_articles, 
                   p.nom_pdv as point_nom,
                   p.adresse_pdv as point_adresse
            FROM commandes c
            LEFT JOIN details_commande dc ON c.id_commande = dc.id_commande
            LEFT JOIN points_de_vente p ON c.point_de_vente_id = p.id_pdv
            GROUP BY c.id_commande
            ORDER BY COALESCE(c.date_creation, c.date_commande) DESC
        ");
        $commandes = $stmt->fetchAll();
        echo "<p>Nombre de commandes récupérées: " . count($commandes) . "</p>";
        
        if (count($commandes) > 0) {
            echo "<h4>Première commande:</h4>";
            echo "<pre>";
            print_r($commandes[0]);
            echo "</pre>";
        }
    } catch (PDOException $e) {
        echo "<p style='color:red;'>❌ Erreur requête: " . $e->getMessage() . "</p>";
    }
    
    // Test 4: Requête alternative simple
    echo "<h3>4. Test requête simple:</h3>";
    try {
        $stmt = $pdo->query("SELECT * FROM commandes ORDER BY id_commande DESC LIMIT 5");
        $commandes = $stmt->fetchAll();
        echo "<p>Commandes (simples): " . count($commandes) . "</p>";
        
        if (count($commandes) > 0) {
            echo "<h4>Première commande:</h4>";
            echo "<pre>";
            print_r($commandes[0]);
            echo "</pre>";
        }
    } catch (PDOException $e) {
        echo "<p style='color:red;'>❌ Erreur: " . $e->getMessage() . "</p>";
    }
    
} catch (PDOException $e) {
    echo "<p style='color:red;'>❌ Erreur connexion: " . $e->getMessage() . "</p>";
}
?>

