<?php
/**
 * logout.php - Déconnexion
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Détruire la session
$_SESSION = [];
session_destroy();

header('Location: index.php');
exit;

