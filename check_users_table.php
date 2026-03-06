<?php
/**
 * Script pour vérifier la structure de la table utilisateurs
 */
$host = 'localhost';
$dbname = 'sepelasLia';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h2>=== STRUCTURE TABLE UTILISATEURS ===</h2>";
    
    // Structure de la table
    $stmt = $pdo->query("DESCRIBE utilisateurs");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<table border='1' style='border-collapse:collapse;'>";
    echo "<tr><th>Field</th><th>Type</th><th>Key</th></tr>";
    foreach ($columns as $col) {
        echo "<tr>";
        echo "<td>" . $col['Field'] . "</td>";
        echo "<td>" . $col['Type'] . "</td>";
        echo "<td>" . $col['Key'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Lister tous les utilisateurs
    echo "<h2>=== TOUS LES UTILISATEURS ===</h2>";
    $stmt = $pdo->query("SELECT * FROM utilisateurs");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (count($users) > 0) {
        echo "<pre>";
        print_r($users);
        echo "</pre>";
    } else {
        echo "<p>Aucun utilisateur trouvé.</p>";
    }
    
} catch (PDOException $e) {
    echo "<p style='color:red;'>❌ Erreur: " . $e->getMessage() . "</p>";
}
?>

