<?php
/**
 * delete_notification.php - Supprimer une notification
 * Table notifications: id_notification, user_id, type, titre, message, lu, date_creation
 */

session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

require_once 'config/db.php';

// Supprimer une notification
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $notif_id = (int)$_GET['id'];
    $stmt = $pdo->prepare("DELETE FROM notifications WHERE id_notification = ? AND user_id = ?");
    $stmt->execute([$notif_id, $user_id]);
}

// Rediriger vers la page notifications
header('Location: notifications.php');
exit;

