<?php
/**
 * check_messages.php - Vérifie les nouvelles réponses aux messages (Chat)
 * Retourne le nombre de messages non lus pour l'utilisateur
 */
require_once 'includes/db.php';

header('Content-Type: application/json');

$user_id = isset($_GET['user_id']) ? (int)$_GET['user_id'] : 0;

if ($user_id <= 0) {
    echo json_encode(['new_count' => 0]);
    exit;
}

try {
    // Compter les messages non lus pour cet utilisateur (messages reçus du système/admin)
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM messages WHERE user_id = ? AND lu = 0");
    $stmt->execute([$user_id]);
    $count = (int)$stmt->fetchColumn();
    
    echo json_encode(['new_count' => $count]);
} catch (PDOException $e) {
    echo json_encode(['new_count' => 0, 'error' => $e->getMessage()]);
}

