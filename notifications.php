<?php
/**
 * notifications.php - Page Notifications Client
 * Table notifications: id_notification, user_id, type, titre, message, lu, date_creation
 * Design: Fond blanc #ffffff, texte noir #000000 avec icônes Font Awesome professionnelles
 */

session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$page_title = 'Mes notifications';

require_once 'config/db.php';

// Compter les non lus
$stmt = $pdo->prepare("SELECT COUNT(*) FROM notifications WHERE user_id = ? AND lu = 0");
$stmt->execute([$user_id]);
$nb_non_lus = (int)$stmt->fetchColumn();

// Récupérer les notifications
$stmt = $pdo->prepare("
    SELECT * FROM notifications 
    WHERE user_id = ? 
    ORDER BY date_creation DESC 
    LIMIT 50
");
$stmt->execute([$user_id]);
$notifications = $stmt->fetchAll();

require_once 'includes/header.php';
?>

<main style="padding: 20px; background: #ffffff; min-height: 100vh;">
    <div style="max-width: 600px; margin: 0 auto;">
        <!-- En-tête -->
        <div style="background: #ffffff; border: 2px solid #000000; border-radius: 12px; padding: 20px; margin-bottom: 20px;">
            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
                <h1 style="font-size: 1.5rem; color: #000000; margin: 0; font-weight: 800;">
                    <i class="fas fa-bell" style="color: #000000;"></i> Notifications
                    <?php if ($nb_non_lus > 0): ?>
                        <span style="background: #ef4444; color: #ffffff; font-size: 0.75rem; padding: 4px 10px; border-radius: 12px; margin-left: 8px; font-weight: 700;">
                            <?php echo $nb_non_lus; ?>
                        </span>
                    <?php endif; ?>
                </h1>
                <?php if ($nb_non_lus > 0): ?>
                    <a href="update_notification.php?all=1" style="background: #000000; color: #ffffff; text-decoration: none; padding: 10px 18px; border-radius: 8px; font-size: 0.9rem; font-weight: 600; display: inline-flex; align-items: center; gap: 6px;">
                        <i class="fas fa-check-double"></i> Tout marquer lu
                    </a>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Liste notifications -->
        <?php if (empty($notifications)): ?>
            <div style="background: #ffffff; border: 2px solid #000000; border-radius: 12px; padding: 60px 20px; text-align: center;">
                <div style="font-size: 4rem; color: #cccccc;"><i class="fas fa-bell-slash"></i></div>
                <h2 style="color: #000000; margin: 20px 0 10px; font-size: 1.2rem; font-weight: 700;">Aucune notification</h2>
                <p style="color: #333333; font-size: 0.95rem;">Vous n'avez pas de notification.</p>
            </div>
        <?php else: ?>
            <div style="background: #ffffff; border: 2px solid #000000; border-radius: 12px; overflow: hidden;">
                <?php foreach ($notifications as $notif): 
                    // Icône selon type
                    $icon = 'fa-info-circle';
                    $icon_color = '#3b82f6'; // bleu
                    if ($notif['type'] === 'commande' || $notif['type'] === 'statut_commande') { $icon = 'fa-box'; $icon_color = '#22c55e'; }
                    elseif ($notif['type'] === 'message' || $notif['type'] === 'reponse') { $icon = 'fa-envelope'; $icon_color = '#8b5cf6'; }
                    elseif ($notif['type'] === 'success') { $icon = 'fa-check-circle'; $icon_color = '#22c55e'; }
                    elseif ($notif['type'] === 'warning') { $icon = 'fa-triangle-exclamation'; $icon_color = '#f59e0b'; }
                    elseif ($notif['type'] === 'error') { $icon = 'fa-circle-xmark'; $icon_color = '#ef4444'; }
                    
                    // Style selon lu/non-lu
                    $bg_style = $notif['lu'] ? '#f5f5f5' : '#e8f4fd';
                    $border_left = $notif['lu'] ? '4px solid #cccccc' : '4px solid #3b82f6';
                ?>
                    <div style="padding: 16px 20px; border-bottom: 1px solid #000000; background: <?php echo $bg_style; ?>; border-left: <?php echo $border_left; ?>;">
                        <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 12px;">
                            <div style="flex: 1; min-width: 0; display: flex; gap: 12px;">
                                <div style="width: 44px; height: 44px; border-radius: 50%; background: <?php echo $icon_color; ?>; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                    <i class="fas <?php echo $icon; ?>" style="color: #ffffff; font-size: 1.2rem;"></i>
                                </div>
                                <div style="flex: 1; min-width: 0;">
                                    <div style="font-weight: 700; color: #000000; margin-bottom: 6px; font-size: 1rem; display: flex; align-items: center; gap: 8px;">
                                        <?php echo htmlspecialchars($notif['titre']); ?>
                                        <?php if (!$notif['lu']): ?>
                                            <span style="width: 8px; height: 8px; background: #3b82f6; border-radius: 50%; display: inline-block;"></span>
                                        <?php endif; ?>
                                    </div>
                                    <div style="color: #000000; font-size: 0.95rem; line-height: 1.5; margin-bottom: 8px; font-weight: 500;">
                                        <?php echo htmlspecialchars($notif['message']); ?>
                                    </div>
                                    <div style="color: #333333; font-size: 0.85rem; font-weight: 500;">
                                        <i class="fas fa-clock" style="margin-right: 4px;"></i>
                                        <?php echo date('d/m/Y à H:i', strtotime($notif['date_creation'])); ?>
                                    </div>
                                </div>
                            </div>
                            
                            <div style="display: flex; flex-direction: column; gap: 8px; flex-shrink: 0;">
                                <?php if (!$notif['lu']): ?>
                                    <a href="update_notification.php?id=<?php echo $notif['id_notification']; ?>" 
                                       style="background: #22c55e; color: #ffffff; text-decoration: none; padding: 10px 14px; border-radius: 8px; font-size: 0.8rem; font-weight: 700; text-align: center; display: block; border: none; cursor: pointer;">
                                        <i class="fas fa-check"></i> Lu
                                    </a>
                                <?php endif; ?>
                                <a href="delete_notification.php?id=<?php echo $notif['id_notification']; ?>" 
                                   onclick="return confirm('Supprimer cette notification?')"
                                   style="background: #ffffff; color: #ef4444; text-decoration: none; padding: 10px 14px; border: 2px solid #ef4444; border-radius: 8px; font-size: 0.8rem; font-weight: 700; text-align: center; display: block;">
                                    <i class="fas fa-trash-can"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
        <!-- Retour -->
        <div style="margin-top: 20px; text-align: center;">
            <a href="index.php" style="background: #000000; color: #ffffff; text-decoration: none; padding: 14px 28px; border-radius: 10px; display: inline-block; font-weight: 700; font-size: 1rem; border: 2px solid #000000;">
                <i class="fas fa-arrow-left"></i> Retour à l'accueil
            </a>
        </div>
    </div>
</main>

<style>
@media (max-width: 480px) {
    main { padding: 10px !important; }
    .notification-item { flex-direction: column !important; }
    .action-buttons { flex-direction: row !important; width: 100%; justify-content: center; }
    .action-buttons a { flex: 1; text-align: center; }
}
</style>

<?php require_once 'includes/footer.php'; ?>

