<?php
/**
 * Script pour recréer le compte admin par défaut
 */
$host = 'localhost';
$dbname = 'sepelasLia';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h2>=== CRÉATION COMPTE ADMIN ===</h2>";
    
    // Email et mot de passe par défaut
    $email = 'admin@sepelaslia.com';
    $mot_de_passe = 'admin1234';
    $mot_de_passe_hash = password_hash($mot_de_passe, PASSWORD_DEFAULT);
    
    // Vérifier d'abord la structure de la table
    $stmt = $pdo->query("DESCRIBE utilisateurs");
    $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo "<p>Colonnes disponibles: " . implode(', ', $columns) . "</p>";
    
    // Vérifier si admin existe déjà
    $stmt = $pdo->prepare("SELECT id_user FROM utilisateurs WHERE email = ?");
    $stmt->execute([$email]);
    $existing = $stmt->fetch();
    
    if ($existing) {
        echo "<p style='color:orange;'>⚠️ Le compte admin existe déjà. Mise à jour du mot de passe...</p>";
        
        // Mise à jour du mot de passe
        $sql = "UPDATE utilisateurs SET mot_de_passe = ? WHERE email = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$mot_de_passe_hash, $email]);
        echo "<p style='color:green;'>✅ Mot de passe admin mis à jour!</p>";
    } else {
        echo "<p style='color:blue;'>➕ Création du compte admin...</p>";
        
        // Insertion du nouvel admin
        $sql = "INSERT INTO utilisateurs (email, nom_complet, mot_de_passe, telephone, role, date_creation) VALUES (?, 'Administrateur', ?, '', 'admin', NOW())";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$email, $mot_de_passe_hash]);
        
        echo "<p style='color:green;'>✅ Compte admin créé avec succès!</p>";
    }
    
    echo "<h3>=== IDENTIFIANTS ADMIN ===</h3>";
    echo "<table border='1' style='border-collapse:collapse; background:#f0f0f0;'>";
    echo "<tr><td><strong>Email:</strong></td><td>admin@sepelaslia.com</td></tr>";
    echo "<tr><td><strong>Mot de passe:</strong></td><td>admin1234</td></tr>";
    echo "</table>";
    
    echo "<p><a href='admin/login.php'>Aller à la page de connexion admin</a></p>";
    
} catch (PDOException $e) {
    echo "<p style='color:red;'>❌ Erreur: " . $e->getMessage() . "</p>";
}
?>

