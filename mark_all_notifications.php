<?php
/**
 * mark_all_notifications.php - Marquer toutes les notifications comme lues
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

try {
    require_once 'config/db.php';
    
    $stmt = $pdo->prepare("UPDATE notifications SET lu = 1 WHERE user_id = ? AND lu = 0");
    $stmt->execute([$_SESSION['user_id']]);
} catch (Exception $e) {
    // Ignore errors
}

header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? 'index.php'));
exit;
