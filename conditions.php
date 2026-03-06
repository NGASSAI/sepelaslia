<?php
/**
 * conditions.php - Conditions d'Utilisation
 */

$page_title = 'Conditions d\'Utilisation - Sepelas & Lia';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'includes/header.php';
?>

<main>
    <section style="background-color: var(--color-light-gray); padding: 3rem 0;">
        <div class="container" style="max-width: 900px;">
            <h1 style="color: var(--color-forest); margin-bottom: 1rem;">
                <i class="fas fa-file-alt"></i> Conditions d'Utilisation
            </h1>
            <p style="color: var(--color-gray);">Dernière mise à jour: <?php echo date('d/m/Y'); ?></p>

            <div style="background: white; padding: 2rem; border-radius: 12px; box-shadow: var(--shadow-light); margin-top: 2rem;">
                
                <h2 style="color: var(--color-forest); margin-top: 2rem; margin-bottom: 1rem;">1. Acceptation des Conditions</h2>
                <p style="color: var(--color-gray); line-height: 1.8;">
                    En accédant à ce site web et en utilisant nos services, vous acceptez d'être lié par ces conditions d'utilisation. Si vous n'acceptez pas ces conditions, veuillez ne pas utiliser ce site.
                </p>

                <h2 style="color: var(--color-forest); margin-top: 2rem; margin-bottom: 1rem;">2. Utilisation du Site</h2>
                <p style="color: var(--color-gray); line-height: 1.8;">
                    Vous acceptez d'utiliser ce site uniquement à des fins légales et de ne pas l'utiliser d'une manière qui pourrait endommager, désactiver, surcharger ou altérer le site ou les serveurs ou réseaux qui y sont connectés.
                </p>

                <h2 style="color: var(--color-forest); margin-top: 2rem; margin-bottom: 1rem;">3. Produits et Services</h2>
                <p style="color: var(--color-gray); line-height: 1.8;">
                    Tous les produits et services offerts sont soumis à la disponibilité. Nous nous réservons le droit de modifier, d'interrompre ou d'annuler tout produit ou service à tout moment sans préavis.
                </p>

                <h2 style="color: var(--color-forest); margin-top: 2rem; margin-bottom: 1rem;">4. Commandes et Paiement</h2>
                <p style="color: var(--color-gray); line-height: 1.8;">
                    Toutes les commandes sont acceptées sous réserve de disponibilité. Le paiement doit être effectué selon les modes proposés. Les prix sont sujets à modification sans préavis.
                </p>

                <h2 style="color: var(--color-forest); margin-top: 2rem; margin-bottom: 1rem;">5. Limitation de Responsabilité</h2>
                <p style="color: var(--color-gray); line-height: 1.8;">
                    Sepelas & Lia ne sera pas responsable de tout dommage indirect, spécial ou consécutif résultant de l'utilisation ou de l'impossibilité d'utiliser les produits ou services.
                </p>

                <h2 style="color: var(--color-forest); margin-top: 2rem; margin-bottom: 1rem;">6. Propriété Intellectuelle</h2>
                <p style="color: var(--color-gray); line-height: 1.8;">
                    Le contenu de ce site, y compris mais sans s'y limiter les textes, graphiques, logos et images, est la propriété de Sepelas & Lia ou de ses fournisseurs de contenu et est protégé par les lois internationales sur le droit d'auteur.
                </p>

                <h2 style="color: var(--color-forest); margin-top: 2rem; margin-bottom: 1rem;">7. Liens Externes</h2>
                <p style="color: var(--color-gray); line-height: 1.8;">
                    Ce site peut contenir des liens vers des sites externes. Nous ne sommes pas responsables du contenu, de la précision ou des pratiques de ces sites externes.
                </p>

                <h2 style="color: var(--color-forest); margin-top: 2rem; margin-bottom: 1rem;">8. Modification des Conditions</h2>
                <p style="color: var(--color-gray); line-height: 1.8;">
                    Nous nous réservons le droit de modifier ces conditions à tout moment. Les modifications seront effectives dès leur publication sur le site.
                </p>

                <div style="background: var(--color-light-gray); padding: 1.5rem; border-radius: 8px; margin-top: 2rem;">
                    <p style="margin: 0; color: var(--color-gray); font-size: 0.9rem;">
                        <strong>Questions?</strong> Contactez-nous à <a href="mailto:nathanngassai885@gmail.com" style="color: var(--color-forest);">nathanngassai885@gmail.com</a>
                    </p>
                </div>
            </div>
        </div>
    </section>
</main>

<?php require_once 'includes/footer.php'; ?>
