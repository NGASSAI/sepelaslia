<?php
/**
 * merci.php - Confirmation de commande
 */

$page_title = 'Merci - Sepelas&Lia';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$ref = $_GET['ref'] ?? '';
$commande = null;
$details = [];

if ($ref) {
    try {
        require_once 'config/db.php';
        
        $stmt = $pdo->prepare("SELECT * FROM commandes WHERE numero_commande = ?");
        $stmt->execute([$ref]);
        $commande = $stmt->fetch();
        
        if ($commande) {
            $cmd_id = $commande['id'] ?? $commande['id_commande'] ?? 0;
            $stmt = $pdo->prepare("SELECT * FROM details_commande WHERE id_commande = ?");
            $stmt->execute([$cmd_id]);
            $details = $stmt->fetchAll();
        }
    } catch (Exception $e) {}
}

require_once 'includes/header.php';
?>

<main>
    <div class="container" style="max-width: 600px;">
        <div class="card animate-fade">
            <div class="card-body text-center">
                <div style="font-size: 4rem; margin-bottom: 1rem;">✅</div>
                <h1 style="font-size: 1.75rem; color: var(--color-forest); margin-bottom: 0.5rem;">
                    Merci pour votre commande!
                </h1>
                <p class="text-muted" style="margin-bottom: 1.5rem;">
                    Votre commande a été enregistrée avec succès
                </p>
                
                <?php if ($commande): ?>
                    <div style="background: var(--color-light-gray); padding: 1.5rem; border-radius: 12px; margin-bottom: 1.5rem; text-align: left;">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                            <span class="text-muted">Numéro de commande:</span>
                            <strong><?php echo htmlspecialchars($commande['numero_commande']); ?></strong>
                        </div>
                        <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                            <span class="text-muted">Date:</span>
                            <span><?php echo date('d/m/Y H:i', strtotime($commande['date_commande'])); ?></span>
                        </div>
                        <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                            <span class="text-muted">Montant total:</span>
                            <strong style="color: var(--color-forest); font-size: 1.25rem;">
                                <?php echo number_format($commande['total_montant'], 0, ',', ' '); ?> F
                            </strong>
                        </div>
                        <div style="display: flex; justify-content: space-between;">
                            <span class="text-muted">Paiement:</span>
                            <span>
                                <?php echo $commande['methode_paiement'] === 'mobile_money' ? '📱 Mobile Money' : '💵 À la livraison'; ?>
                            </span>
                        </div>
                    
                    <!-- Instructions paiement -->
                    <?php if ($commande['methode_paiement'] === 'mobile_money'): ?>
                        <div style="background: #fff3cd; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; text-align: left;">
                            <h3 style="color: #856404; margin-bottom: 0.75rem;">📱 Instructions Mobile Money</h3>
                            <ol style="padding-left: 1.25rem; color: #856404; font-size: 0.9rem; line-height: 1.8;">
                                <li>Envoyez <strong><?php echo number_format($commande['total_montant'], 0, ',', ' '); ?> F</strong> au <strong>+242 066 817 726</strong></li>
                                <li>Utilisez votre numéro de commande comme référence</li>
                                <li>Nous confirmerons votre paiement sous 24h</li>
                            </ol>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Contact -->
                    <div style="display: flex; gap: 10px; margin-bottom: 1rem;">
                        <a href="tel:+242066817726" class="btn btn-primary" style="flex: 1; text-align: center;">
                            <i class="fas fa-phone"></i> Appeler
                        </a>
                        <a href="https://wa.me/242066817726?text=Bonjour, je confirme ma commande <?php echo htmlspecialchars($ref); ?>" 
                           class="btn btn-secondary" 
                           target="_blank"
                           style="flex: 1; text-align: center;">
                            <i class="fab fa-whatsapp"></i> WhatsApp
                        </a>
                    </div>
                <?php endif; ?>
                
                <a href="index.php" class="btn btn-primary">
                    Retour à l'accueil
                </a>
            </div>
    </div>
</main>

<?php require_once 'includes/footer.php'; ?>
