<?php
/**
 * confirmation.php - Page de confirmation de commande
 * Affiche les détails de la commande créée
 */

require_once 'includes/db.php';

$page_title = 'SepelasLia - Confirmation de commande';

// Récupérer les détails de la commande (id_commande)
$commande_id = isset($_GET['ref']) ? (int)$_GET['ref'] : 0;

if (empty($commande_id)) {
    header('Location: index.php');
    exit;
}

// Récupérer les détails de la commande
$commande = null;
$details = [];

try {
    $stmt = $pdo->prepare("
    SELECT c.*, p.nom_pdv as point_nom, p.adresse_pdv as lieu, p.telephone_pdv as point_tel
    FROM commandes c
    LEFT JOIN points_de_vente p ON c.point_de_vente_id = p.id_pdv
    WHERE c.id_commande = ?
    ");
    $stmt->execute([$commande_id]);
    $commande = $stmt->fetch();

    if ($commande) {
        // Générer numero_commande et date_creation s'ils n'existent pas (anciennes commandes)
        if (empty($commande['numero_commande'])) {
            $commande['numero_commande'] = 'CMD-' . $commande_id . '-' . date('Ymd', strtotime($commande['date_commande'] ?? 'now'));
        }
        if (empty($commande['date_creation'])) {
            $commande['date_creation'] = $commande['date_commande'] ?? date('Y-m-d H:i:s');
        }
        
        $stmt = $pdo->prepare("
            SELECT d.*, pr.nom as produit_nom, pr.image as produit_image
            FROM details_commande d
            LEFT JOIN produits pr ON d.id_produit = pr.id_produit
            WHERE d.id_commande = ?
            ORDER BY d.id_detail
        ");
        $stmt->execute([$commande_id]);
        $details = $stmt->fetchAll();
    }
} catch (PDOException $e) {
    error_log('Erreur confirmation: ' . $e->getMessage());
}

require_once 'includes/header.php';
?>

<main>
    <section style="background-color: var(--color-light-gray);">
        <div class="container">
            <?php if (!$commande): ?>
                <div style="text-align: center; padding: 3rem 1rem;">
                    <div style="font-size: 3rem; margin-bottom: 1rem;">❌</div>
                    <h1 style="color: var(--color-gray); margin-bottom: 1rem;">Commande non trouvée</h1>
                    <a href="index.php" class="btn btn-primary">Retour à l'accueil</a>
                </div>
            <?php else: ?>
                <!-- En-tête succès -->
                <div style="text-align: center; margin-bottom: 2rem; padding: 2rem; background: white; border-radius: 12px; box-shadow: var(--shadow-light);">
                    <div style="font-size: 4rem; margin-bottom: 1rem;">✅</div>
                    <h1 style="color: #28a745; margin-bottom: 0.5rem;">Commande confirmée!</h1>
                    <p style="color: var(--color-gray); font-size: 1.1rem;">
                        Merci pour votre commande. Vous recevrez un SMS de confirmation très bientôt.
                    </p>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 350px; gap: 2rem; margin-bottom: 2rem;">
                    <!-- Détails commande -->
                    <div>
                        <!-- Numéro de commande -->
                        <div style="background: white; padding: 1.5rem; border-radius: 12px; box-shadow: var(--shadow-light); margin-bottom: 1.5rem; border-left: 4px solid var(--color-terracotta);">
                            <h3 style="color: var(--color-gray); margin-bottom: 0.5rem; font-size: 0.9rem;">Numéro de commande</h3>
                            <p style="font-size: 1.5rem; font-weight: 700; color: var(--color-forest); font-family: monospace;">
                                <?php echo htmlspecialchars($commande['numero_commande'] ?? 'N/A'); ?>
                            </p>
                            <small style="color: #666; display: block; margin-top: 0.5rem;">
                                Conservez ce numéro pour suivre votre commande
                            </small>
                        </div>

                        <!-- Informations client -->
                        <div style="background: white; padding: 1.5rem; border-radius: 12px; box-shadow: var(--shadow-light); margin-bottom: 1.5rem;">
                            <h3 style="color: var(--color-forest); margin-bottom: 1rem;">👤 Informations client</h3>
                            <div style="display: grid; grid-template-columns: 1fr; gap: 1rem; color: var(--color-gray);">
                                <div>
                                    <strong>Nom:</strong><br>
                                    <?php echo htmlspecialchars($commande['nom_client'] ?? ''); ?>
                                </div>
                                <div>
                                    <strong>Téléphone:</strong><br>
                                    <?php echo htmlspecialchars($commande['telephone_client'] ?? ''); ?>
                                </div>
                                <div>
                                    <strong>Date:</strong><br>
                                    <?php 
                                    $date_cmd = $commande['date_creation'] ?? $commande['date_commande'] ?? date('Y-m-d H:i:s');
                                    echo date('d/m/Y à H:i', strtotime($date_cmd)); 
                                    ?>
                                </div>
                        </div>

                        <!-- Point de retrait -->
                        <div style="background: white; padding: 1.5rem; border-radius: 12px; box-shadow: var(--shadow-light); margin-bottom: 1.5rem; border-left: 4px solid var(--color-forest);">
                            <h3 style="color: var(--color-forest); margin-bottom: 1rem;">📍 Point de retrait</h3>
                            <h4 style="color: var(--color-dark); margin-bottom: 0.5rem;"><?php echo htmlspecialchars($commande['point_nom'] ?? 'Point de vente'); ?></h4>
                            <p style="color: var(--color-gray); margin-bottom: 0.5rem;">
                                📍 <?php echo htmlspecialchars($commande['lieu'] ?? ''); ?>
                            </p>
                            <?php if (!empty($commande['point_tel'])): ?>
                                <p style="color: var(--color-gray); margin-bottom: 0.5rem;">
                                    📱 <?php echo htmlspecialchars($commande['point_tel']); ?>
                                </p>
                            <?php endif; ?>
                            <div style="background: var(--color-light-gray); padding: 0.75rem; border-radius: 6px; margin-top: 1rem; font-size: 0.9rem;">
                                <strong>Statut:</strong> <span style="color: #ffc107;">En attente de confirmation</span>
                            </div>

                        <!-- Articles commandés -->
                        <div style="background: white; padding: 1.5rem; border-radius: 12px; box-shadow: var(--shadow-light);">
                            <h3 style="color: var(--color-forest); margin-bottom: 1rem;">📦 Articles commandés</h3>
                            <?php if (empty($details)): ?>
                                <p style="color: var(--color-gray);">Aucun article trouvé</p>
                            <?php else: ?>
                                <?php foreach ($details as $detail): ?>
                                    <div style="display: flex; justify-content: space-between; padding: 0.75rem 0; border-bottom: 1px solid var(--color-border); align-items: center;">
                                        <div>
                                            <strong><?php echo htmlspecialchars($detail['produit_nom'] ?? 'Produit'); ?></strong><br>
<small style="color: var(--color-gray);">
                                                ×<?php echo $detail['quantite'] ?? 1; ?> @ ₣<?php echo number_format($detail['prix_unitaire'] ?? 0, 0, ',', ' '); ?>
                                            </small>
                                        </div>
                                        <div style="font-weight: 600; color: var(--color-terracotta);">
                                            ₣<?php echo number_format($detail['sous_total'] ?? 0, 0, ',', ' '); ?>
                                        </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>

                    <!-- Résumé paiement (Sidebar) -->
                    <div style="height: fit-content; position: sticky; top: 90px;">
                        <div style="background: white; border-radius: 12px; padding: 1.5rem; box-shadow: var(--shadow-light); margin-bottom: 1.5rem;">
                            <h3 style="margin-bottom: 1rem; color: var(--color-forest);">💰 Résumé paiement</h3>
                            
                            <div style="margin-bottom: 1rem; padding-bottom: 1rem; border-bottom: 1px solid var(--color-border);">
                                <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem; color: var(--color-gray);">
                                    <span>Sous-total:</span>
                                    <span>₣<?php echo number_format($commande['total_montant'] ?? 0, 0, ',', ' '); ?></span>
                                </div>
                                <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem; color: var(--color-gray);">
                                    <span>Livraison:</span>
                                    <span>Gratuit 🎁</span>
                                </div>

                            <div style="display: flex; justify-content: space-between; font-size: 1.5rem; font-weight: 700; color: var(--color-terracotta); margin-bottom: 1.5rem;">
                                <span>Total:</span>
                                <span>₣<?php echo number_format($commande['total_montant'] ?? 0, 0, ',', ' '); ?></span>
                            </div>

                            <div style="background: #d4edda; border: 1px solid #c3e6cb; border-radius: 8px; padding: 1rem; color: #155724; font-size: 0.9rem; margin-bottom: 1.5rem;">
                                <strong>✓ Commande enregistrée</strong><br>
                                Vous recevrez un SMS avec les détails du paiement
                            </div>

                            <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                                <a href="panier.php" class="btn btn-secondary" style="text-align: center; width: 100%;">
                                    🛒 Retour au panier
                                </a>
                                <a href="index.php" class="btn btn-outline" style="text-align: center; width: 100%;">
                                    🏠 Accueil
                                </a>
                            </div>

                        <!-- Prochaines étapes -->
                        <div style="background: white; border-radius: 12px; padding: 1.5rem; box-shadow: var(--shadow-light);">
                            <h3 style="color: var(--color-forest); margin-bottom: 1rem;">📋 Prochaines étapes</h3>
                            <ol style="color: var(--color-gray); padding-left: 1.5rem; line-height: 1.8;">
                                <li>Vous recevrez un SMS de confirmation</li>
                                <li>Effectuez le paiement selon les instructions</li>
                                <li>Retrait de votre commande au point convenu</li>
                                <li>Profitez de vos produits! 🎉</li>
                            </ol>
                            
                            <div style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid var(--color-border); font-size: 0.9rem;">
                                <strong>💬 Questions?</strong><br>
                                <div style="display: flex; gap: 10px; margin-top: 10px; flex-wrap: wrap;">
                                    <a href="tel:+242066817726" style="display: inline-flex; align-items: center; gap: 5px; padding: 8px 12px; background: #1a4d2e; color: white; border-radius: 6px; text-decoration: none; font-weight: 600;">
                                        <i class="fas fa-phone"></i> Appeler
                                    </a>
                                    <a href="https://wa.me/242066817726?text=Ref%3A%20<?php echo $commande_id; ?>" target="_blank" style="display: inline-flex; align-items: center; gap: 5px; padding: 8px 12px; background: #25D366; color: white; border-radius: 6px; text-decoration: none; font-weight: 600;">
                                        <i class="fab fa-whatsapp"></i> WhatsApp
                                    </a>
                                </div>
                            </div>
                    </div>

            <?php endif; ?>
        </div>
    </section>
</main>

<?php require_once 'includes/footer.php'; ?>
