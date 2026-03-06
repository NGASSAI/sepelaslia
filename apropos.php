<?php
/**
 * apropos.php - Page À Propos
 */

$page_title = 'À Propos - Sepelas & Lia';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'includes/header.php';
?>

<main>
    <section style="background: linear-gradient(135deg, var(--color-forest) 0%, #0d3a1f 100%); color: white; padding: 4rem 0; text-align: center;">
        <div class="container">
            <h1 style="font-size: 2.5rem; margin-bottom: 1rem;">🌿 À Propos de Sepelas & Lia</h1>
            <p style="font-size: 1.1rem; opacity: 0.95;">Votre partenaire de confiance pour des produits naturels de qualité</p>
        </div>
    </section>

    <section style="padding: 3rem 0;">
        <div class="container" style="max-width: 900px;">
            <div style="background: white; padding: 2rem; border-radius: 12px; box-shadow: var(--shadow-light);">
                
                <h2 style="color: var(--color-forest); margin-bottom: 1.5rem;">Notre Histoire</h2>
                <p style="color: var(--color-gray); line-height: 1.8; margin-bottom: 1.5rem;">
                    Sepelas & Lia est née d'une passion pour les produits naturels et le bien-être. Depuis sa création, notre mission est de mettre à la disposition de nos clients des produits de qualité supérieure, issus de sources fiables et respectueuses de l'environnement.
                </p>
                <p style="color: var(--color-gray); line-height: 1.8; margin-bottom: 2rem;">
                    Basée à Brazzaville, notre entreprise s'engage à offrir une expérience d'achat transparente, sécurisée et agréable à tous nos clients.
                </p>

                <hr style="border: none; border-top: 2px solid var(--color-border); margin: 2rem 0;">

                <h2 style="color: var(--color-forest); margin-bottom: 1.5rem;">Nos Valeurs</h2>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-bottom: 2rem;">
                    <div style="padding: 1.5rem; background: var(--color-light-gray); border-radius: 8px;">
                        <h3 style="color: var(--color-forest); margin-bottom: 0.75rem;">
                            <i class="fas fa-check-circle" style="color: var(--color-success); margin-right: 0.5rem;"></i>Qualité
                        </h3>
                        <p style="color: var(--color-gray); margin: 0; font-size: 0.95rem;">
                            Nous garantissons la meilleure qualité pour tous nos produits
                        </p>
                    </div>
                    <div style="padding: 1.5rem; background: var(--color-light-gray); border-radius: 8px;">
                        <h3 style="color: var(--color-forest); margin-bottom: 0.75rem;">
                            <i class="fas fa-shield-alt" style="color: var(--color-success); margin-right: 0.5rem;"></i>Confiance
                        </h3>
                        <p style="color: var(--color-gray); margin: 0; font-size: 0.95rem;">
                            Transparence et sécurité dans chaque transaction
                        </p>
                    </div>
                    <div style="padding: 1.5rem; background: var(--color-light-gray); border-radius: 8px;">
                        <h3 style="color: var(--color-forest); margin-bottom: 0.75rem;">
                            <i class="fas fa-leaf" style="color: var(--color-success); margin-right: 0.5rem;"></i>Naturel
                        </h3>
                        <p style="color: var(--color-gray); margin: 0; font-size: 0.95rem;">
                            Produits 100% naturels et respectueux de l'environnement
                        </p>
                    </div>
                    <div style="padding: 1.5rem; background: var(--color-light-gray); border-radius: 8px;">
                        <h3 style="color: var(--color-forest); margin-bottom: 0.75rem;">
                            <i class="fas fa-headset" style="color: var(--color-success); margin-right: 0.5rem;"></i>Service
                        </h3>
                        <p style="color: var(--color-gray); margin: 0; font-size: 0.95rem;">
                            Support client réactif et disponible 24/7
                        </p>
                    </div>
                </div>

                <hr style="border: none; border-top: 2px solid var(--color-border); margin: 2rem 0;">

                <h2 style="color: var(--color-forest); margin-bottom: 1.5rem;">Pourquoi Nous Choisir?</h2>
                <ul style="color: var(--color-gray); line-height: 2; margin-bottom: 2rem;">
                    <li><i class="fas fa-check" style="color: var(--color-success); margin-right: 0.75rem;"></i><strong>Produits vérifiés</strong> - Tous nos produits sont soigneusement sélectionnés</li>
                    <li><i class="fas fa-check" style="color: var(--color-success); margin-right: 0.75rem;"></i><strong>Livraison rapide</strong> - Livraison gratuite à Brazzaville</li>
                    <li><i class="fas fa-check" style="color: var(--color-success); margin-right: 0.75rem;"></i><strong>Sécurité du paiement</strong> - Plusieurs modes de paiement sécurisés</li>
                    <li><i class="fas fa-check" style="color: var(--color-success); margin-right: 0.75rem;"></i><strong>Satisfaction garantie</strong> - Politique de remboursement flexible</li>
                </ul>

                <div style="background: linear-gradient(135deg, var(--color-forest) 0%, #0d3a1f 100%); color: white; padding: 2rem; border-radius: 12px; text-align: center;">
                    <h3 style="margin: 0 0 1rem 0;">Vous avez des questions?</h3>
                    <p style="margin: 0 0 1.5rem 0; opacity: 0.95;">
                        Notre équipe est là pour vous aider
                    </p>
                    <a href="contact.php" class="btn btn-primary" style="display: inline-block; padding: 0.75rem 2rem; background: white; color: var(--color-forest); text-decoration: none; font-weight: 600; border-radius: 8px;">
                        <i class="fas fa-envelope"></i> Nous Contacter
                    </a>
                </div>
            </div>
        </div>
    </section>
</main>

<?php require_once 'includes/footer.php'; ?>
