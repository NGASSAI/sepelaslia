<?php
/**
 * Script pour vérifier la structure de la table commandes
 */
$host = 'localhost';
$dbname = 'sepelasLia';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h2>Structure de la table commandes:</h2>";
    $stmt = $pdo->query("DESCRIBE commandes");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<table border='1' style='border-collapse:collapse;'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
    foreach ($columns as $col) {
        echo "<tr>";
        echo "<td>" . $col['Field'] . "</td>";
        echo "<td>" . $col['Type'] . "</td>";
        echo "<td>" . $col['Null'] . "</td>";
        echo "<td>" . $col['Key'] . "</td>";
        echo "<td>" . $col['Default'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    echo "<h2>Premières commandes:</h2>";
    $stmt = $pdo->query("SELECT * FROM commandes LIMIT 3");
    $commandes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<pre>";
    print_r($commandes);
    echo "</pre>";
    
} catch (PDOException $e) {
    echo "<p style='color:red;'>❌ Erreur: " . $e->getMessage() . "</p>";
}
?>

