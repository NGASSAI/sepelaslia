<?php
/**
 * update_notification.php - Marquer notification comme lu
 * Table notifications: id_notification, user_id, type, titre, message, lu, date_creation
 */

session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

require_once 'config/db.php';

// Marquer une notification comme lu
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $notif_id = (int)$_GET['id'];
    $stmt = $pdo->prepare("UPDATE notifications SET lu = 1 WHERE id_notification = ? AND user_id = ?");
    $stmt->execute([$notif_id, $user_id]);
}

// Marquer toutes comme lu
if (isset($_GET['all']) && $_GET['all'] == 1) {
    $stmt = $pdo->prepare("UPDATE notifications SET lu = 1 WHERE user_id = ? AND lu = 0");
    $stmt->execute([$user_id]);
}

// Rediriger vers la page notifications
header('Location: notifications.php');
exit;

