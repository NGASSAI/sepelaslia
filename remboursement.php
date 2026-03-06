<?php
/**
 * remboursement.php - Politique de Remboursement
 */

$page_title = 'Politique de Remboursement - Sepelas & Lia';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'includes/header.php';
?>

<main>
    <section style="background-color: var(--color-light-gray); padding: 3rem 0;">
        <div class="container" style="max-width: 900px;">
            <h1 style="color: var(--color-forest); margin-bottom: 1rem;">
                <i class="fas fa-exchange-alt"></i> Politique de Remboursement
            </h1>
            <p style="color: var(--color-gray);">Dernière mise à jour: <?php echo date('d/m/Y'); ?></p>

            <div style="background: white; padding: 2rem; border-radius: 12px; box-shadow: var(--shadow-light); margin-top: 2rem;">
                
                <h2 style="color: var(--color-forest); margin-top: 0; margin-bottom: 1rem;">Garantie de Satisfaction</h2>
                <p style="color: var(--color-gray); line-height: 1.8;">
                    Chez Sepelas & Lia, votre satisfaction est notre priorité. Nous vous offrons une garantie complète sur tous nos produits.
                </p>

                <h2 style="color: var(--color-forest); margin-top: 2rem; margin-bottom: 1rem;">Délai de Rétractation</h2>
                <p style="color: var(--color-gray); line-height: 1.8;">
                    Vous avez <strong>14 jours à partir de la réception</strong> de votre commande pour demander un remboursement, sans raison particulière.
                </p>

                <h2 style="color: var(--color-forest); margin-top: 2rem; margin-bottom: 1rem;">Conditions de Remboursement</h2>
                <ul style="color: var(--color-gray); line-height: 2;">
                    <li><i class="fas fa-check" style="color: var(--color-success); margin-right: 0.75rem;"></i>Les produits doivent être dans leur état original</li>
                    <li><i class="fas fa-check" style="color: var(--color-success); margin-right: 0.75rem;"></i>Tous les emballages et documents doivent être intacts</li>
                    <li><i class="fas fa-check" style="color: var(--color-success); margin-right: 0.75rem;"></i>Les produits alimentaires ouverts ne sont pas remboursables</li>
                    <li><i class="fas fa-check" style="color: var(--color-success); margin-right: 0.75rem;"></i>Preuve d'achat requise</li>
                </ul>

                <h2 style="color: var(--color-forest); margin-top: 2rem; margin-bottom: 1rem;">Processus de Remboursement</h2>
                <div style="background: var(--color-light-gray); padding: 1.5rem; border-radius: 8px; margin-bottom: 1.5rem;">
                    <p style="margin: 0 0 0.75rem 0; color: var(--color-gray);">
                        <strong>Étape 1:</strong> Contactez-nous avec votre numéro de commande
                    </p>
                    <p style="margin: 0 0 0.75rem 0; color: var(--color-gray);">
                        <strong>Étape 2:</strong> Nous vous confirmons la réception de votre demande
                    </p>
                    <p style="margin: 0 0 0.75rem 0; color: var(--color-gray);">
                        <strong>Étape 3:</strong> Nous traitons votre remboursement (3-7 jours)
                    </p>
                    <p style="margin: 0; color: var(--color-gray);">
                        <strong>Étape 4:</strong> Vous êtes remboursé sur le compte utilisé
                    </p>
                </div>

                <h2 style="color: var(--color-forest); margin-top: 2rem; margin-bottom: 1rem;">Produits Défectueux</h2>
                <p style="color: var(--color-gray); line-height: 1.8;">
                    Si vous recevez un produit défectueux ou endommagé, contactez-nous immédiatement avec des photos. Nous vous proposerons un remplacement ou un remboursement complet.
                </p>

                <h2 style="color: var(--color-forest); margin-top: 2rem; margin-bottom: 1rem;">Frais de Retour</h2>
                <p style="color: var(--color-gray); line-height: 1.8;">
                    Les frais de renvoi sont à votre charge, sauf en cas de produit défectueux. Nous offrons un retour gratuit pour les articles endommagés.
                </p>

                <div style="background: linear-gradient(135deg, var(--color-forest) 0%, #0d3a1f 100%); color: white; padding: 2rem; border-radius: 12px; text-align: center; margin-top: 2rem;">
                    <h3 style="margin: 0 0 1rem 0;">Des Questions?</h3>
                    <p style="margin: 0 0 1.5rem 0; opacity: 0.95;">
                        Notre équipe support est disponible pour vous aider
                    </p>
                    <a href="contact.php" class="btn btn-primary" style="display: inline-block; padding: 0.75rem 2rem; background: white; color: var(--color-forest); text-decoration: none; font-weight: 600; border-radius: 8px;">
                        <i class="fas fa-headset"></i> Contacter le Support
                    </a>
                </div>
            </div>
        </div>
    </section>
</main>

<?php require_once 'includes/footer.php'; ?>
