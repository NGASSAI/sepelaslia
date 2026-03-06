<?php
/**
 * Script pour vérifier les identifiants admin
 */
$host = 'localhost';
$dbname = 'sepelasLia';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h2>=== COMPTES ADMIN ===</h2>";
    
    // Vérifier les utilisateurs avec role admin
    $stmt = $pdo->query("SELECT id, nom, email, telephone, role FROM utilisateurs WHERE role = 'admin'");
    $admins = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (count($admins) > 0) {
        echo "<h3>Comptes admin trouvés:</h3>";
        echo "<table border='1' style='border-collapse:collapse;'>";
        echo "<tr><th>ID</th><th>Nom</th><th>Email</th><th>Téléphone</th><th>Rôle</th></tr>";
        foreach ($admins as $admin) {
            echo "<tr>";
            echo "<td>" . $admin['id'] . "</td>";
            echo "<td>" . htmlspecialchars($admin['nom']) . "</td>";
            echo "<td>" . htmlspecialchars($admin['email']) . "</td>";
            echo "<td>" . htmlspecialchars($admin['telephone']) . "</td>";
            echo "<td>" . $admin['role'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        echo "<h3>Note:</h3>";
        echo "<p>Les mots de passe sont hashés (non visibles pour des raisons de sécurité).</p>";
        echo "<p>Pour réinitialiser le mot de passe admin, utilisez la page de connexion ou contactez le support.</p>";
    } else {
        echo "<p style='color:red;'>Aucun compte admin trouvé!</p>";
        echo "<p>Vous pouvez créer un compte admin via la page d'inscription ou le script setup.</p>";
    }
    
    echo "<h3>Tous les utilisateurs:</h3>";
    $stmt = $pdo->query("SELECT id, nom, email, role FROM utilisateurs");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<table border='1' style='border-collapse:collapse;'>";
    echo "<tr><th>ID</th><th>Nom</th><th>Email</th><th>Rôle</th></tr>";
    foreach ($users as $u) {
        echo "<tr>";
        echo "<td>" . $u['id'] . "</td>";
        echo "<td>" . htmlspecialchars($u['nom']) . "</td>";
        echo "<td>" . htmlspecialchars($u['email']) . "</td>";
        echo "<td>" . $u['role'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
} catch (PDOException $e) {
    echo "<p style='color:red;'>❌ Erreur: " . $e->getMessage() . "</p>";
}
?>

