<?php
/**
 * mark_notification_read.php - Marquer notification comme lue
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Non connecté']);
    exit;
}

$id = (int)($_GET['id'] ?? 0);

if ($id > 0) {
    try {
        require_once 'config/db.php';
        
        $stmt = $pdo->prepare("UPDATE notifications SET lu = 1 WHERE id_notification = ? AND user_id = ?");
        $stmt->execute([$id, $_SESSION['user_id']]);
        
        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'ID invalide']);
}
