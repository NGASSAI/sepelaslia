<?php
require_once 'includes/db.php';

$users = $pdo->query('SELECT id_user, email, role FROM utilisateurs')->fetchAll(PDO::FETCH_ASSOC);
echo "Utilisateurs disponibles:\n";
foreach ($users as $u) {
    echo "  - ID: " . $u['id_user'] . ", Email: " . $u['email'] . ", Role: " . $u['role'] . "\n";
}
?>
