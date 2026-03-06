<?php
/**
 * check_notifications.php - Vérifie les nouvelles notifications (Admin & Client)
 * Requête légère: juste un SELECT COUNT(*)
 */
require_once 'includes/db.php';

header('Content-Type: application/json');

$user_id = isset($_GET['user_id']) ? (int)$_GET['user_id'] : 0;

if ($user_id <= 0) {
    echo json_encode(['new_count' => 0]);
    exit;
}

try {
    // Compter les notifications non lues pour cet utilisateur
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM notifications WHERE user_id = ? AND lu = 0");
    $stmt->execute([$user_id]);
    $count = (int)$stmt->fetchColumn();
    
    echo json_encode(['new_count' => $count]);
} catch (PDOException $e) {
    echo json_encode(['new_count' => 0, 'error' => $e->getMessage()]);
}
