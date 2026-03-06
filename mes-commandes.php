<?php
/**
 * mes-commandes.php - Mes Commandes Client
 * Table commandes: id_commande, id_user, total_montant, statut, date_commande, numero_commande, nom_client, telephone_client
 */

session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$page_title = 'Mes commandes';

require_once 'config/db.php';

// Récupérer les commandes avec id_user (liaison directe utilisateur-commande)
$stmt = $pdo->prepare("
    SELECT * FROM commandes 
    WHERE id_user = ? 
    ORDER BY date_commande DESC 
    LIMIT 20
");
$stmt->execute([$user_id]);
$commandes = $stmt->fetchAll();

// Statuts valides
$statut_labels = [
    'en_attente' => 'En attente',
    'confirmee' => 'Confirmée',
    'retrait' => 'Prête',
    'completee' => 'Terminée',
    'paye' => 'Payée',
    'livre' => 'Livrée',
    'annule' => 'Annulée',
    'annulee' => 'Annulée'
];

// Couleurs par statut
$statut_colors = [
    'en_attente' => ['bg' => '#fff3cd', 'color' => '#856404'],
    'confirmee' => ['bg' => '#cce5ff', 'color' => '#004085'],
    'retrait' => ['bg' => '#d4edda', 'color' => '#155724'],
    'completee' => ['bg' => '#d4edda', 'color' => '#155724'],
    'paye' => ['bg' => '#d4edda', 'color' => '#155724'],
    'livre' => ['bg' => '#d4edda', 'color' => '#155724'],
    'annule' => ['bg' => '#f8d7da', 'color' => '#721c25'],
    'annulee' => ['bg' => '#f8d7da', 'color' => '#721c25']
];

require_once 'includes/header.php';
?>

<main style="padding: 20px; background: #fff; min-height: 100vh;">
    <div style="max-width: 800px; margin: 0 auto;">
        <h1 style="font-size: 1.5rem; color: #000; margin-bottom: 20px;">
            <i class="fas fa-box-open"></i> Mes commandes
        </h1>
        
        <?php if (empty($commandes)): ?>
            <div style="text-align: center; padding: 60px 20px; background: white; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                <div style="font-size: 4rem; opacity: 0.3;">📦</div>
                <h2 style="color: #000; margin: 20px 0 10px;">Aucune commande</h2>
                <p style="color: #666;">Vous n'avez pas encore passé de commande.</p>
                <a href="produit.php" style="display: inline-block; background: #1a4d2e; color: white; padding: 12px 24px; border-radius: 8px; text-decoration: none; margin-top: 20px;">
                    Voir les produits
                </a>
            </div>
        <?php else: ?>
            <div style="display: flex; flex-direction: column; gap: 15px;">
                <?php foreach ($commandes as $cmd): 
                    $statut = $cmd['statut'] ?? 'en_attente';
                    $statut_label = $statut_labels[$statut] ?? $statut;
                    $colors = $statut_colors[$statut] ?? ['bg' => '#f3f4f6', 'color' => '#666'];
                ?>
                    <div style="background: white; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); overflow: hidden;">
                        <div style="background: #1a4d2e; color: white; padding: 15px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
                            <div>
                                <div style="font-weight: 700; font-size: 1.1rem;">
                                    <?php echo htmlspecialchars($cmd['numero_commande'] ?? 'CMD-' . $cmd['id_commande']); ?>
                                </div>
                                <div style="font-size: 0.85rem; opacity: 0.9;">
                                    <?php echo date('d/m/Y à H:i', strtotime($cmd['date_commande'])); ?>
                                </div>
                            </div>
                            <div style="font-size: 1.3rem; font-weight: 800;">
                                <?php echo number_format($cmd['total_montant'], 0, ',', ' '); ?> F
                            </div>
                        </div>
                        
                        <div style="padding: 15px;">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                                <span style="padding: 5px 12px; border-radius: 20px; font-size: 0.8rem; font-weight: 600; background: <?php echo $colors['bg']; ?>; color: <?php echo $colors['color']; ?>;">
                                    <?php echo $statut_label; ?>
                                </span>
                                <span style="color: #666; font-size: 0.85rem;">
                                    <?php if (!empty($cmd['telephone_client'])): ?>
                                        <i class="fas fa-phone"></i> <?php echo htmlspecialchars($cmd['telephone_client']); ?>
                                    <?php endif; ?>
                                </span>
                            </div>
                            
                            <!-- Message selon le statut -->
                            <?php if ($statut === 'en_attente'): ?>
                                <div style="background: #fff3cd; padding: 10px; border-radius: 8px; font-size: 0.85rem; color: #856404;">
                                    <i class="fas fa-clock"></i> Votre commande est en attente de validation par notre équipe.
                                </div>
                            <?php elseif ($statut === 'confirmee'): ?>
                                <div style="background: #cce5ff; padding: 10px; border-radius: 8px; font-size: 0.85rem; color: #004085;">
                                    <i class="fas fa-check"></i> Votre commande a été confirmée. Nous préparons votre colis.
                                </div>
                            <?php elseif ($statut === 'retrait'): ?>
                                <div style="background: #d4edda; padding: 10px; border-radius: 8px; font-size: 0.85rem; color: #155724;">
                                    <i class="fas fa-store"></i> Votre commande est prête ! Vous pouvez venir la retirer.
                                </div>
                            <?php elseif ($statut === 'completee' || $statut === 'livre'): ?>
                                <div style="background: #d4edda; padding: 10px; border-radius: 8px; font-size: 0.85rem; color: #155724;">
                                    <i class="fas fa-check-circle"></i> Merci pour votre achat ! Votre commande a été livrée.
                                </div>
                            <?php elseif ($statut === 'annule' || $statut === 'annulee'): ?>
                                <div style="background: #f8d7da; padding: 10px; border-radius: 8px; font-size: 0.85rem; color: #721c25;">
                                    <i class="fas fa-times-circle"></i> Cette commande a été annulée.
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <!-- Auto-refresh every 10 seconds to see status updates -->
            <script>
            // Refresh page every 10 seconds to check for new status
            setTimeout(function() {
                location.reload();
            }, 10000);
            </script>
        <?php endif; ?>
    </div>
</main>

<style>
@media (max-width: 480px) {
    main { padding: 10px !important; }
}
</style>

<?php require_once 'includes/footer.php'; ?>

