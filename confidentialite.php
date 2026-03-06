<?php
/**
 * confidentialite.php - Politique de Confidentialité
 */

$page_title = 'Politique de Confidentialité - Sepelas & Lia';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'includes/header.php';
?>

<main>
    <section style="background-color: var(--color-light-gray); padding: 3rem 0;">
        <div class="container" style="max-width: 900px;">
            <h1 style="color: var(--color-forest); margin-bottom: 1rem;">
                <i class="fas fa-lock"></i> Politique de Confidentialité
            </h1>
            <p style="color: var(--color-gray);">Dernière mise à jour: <?php echo date('d/m/Y'); ?></p>

            <div style="background: white; padding: 2rem; border-radius: 12px; box-shadow: var(--shadow-light); margin-top: 2rem;">
                
                <h2 style="color: var(--color-forest); margin-top: 2rem; margin-bottom: 1rem;">1. Introduction</h2>
                <p style="color: var(--color-gray); line-height: 1.8;">
                    Sepelas & Lia respecte votre vie privée. Cette politique de confidentialité explique comment nous collectons, utilisons et protégeons vos informations personnelles.
                </p>

                <h2 style="color: var(--color-forest); margin-top: 2rem; margin-bottom: 1rem;">2. Informations Collectées</h2>
                <p style="color: var(--color-gray); line-height: 1.8;">
                    Nous collectons les informations suivantes:
                </p>
                <ul style="color: var(--color-gray); line-height: 2;">
                    <li>Nom et prénom</li>
                    <li>Adresse email</li>
                    <li>Numéro de téléphone</li>
                    <li>Adresse de livraison</li>
                    <li>Historique des commandes</li>
                </ul>

                <h2 style="color: var(--color-forest); margin-top: 2rem; margin-bottom: 1rem;">3. Utilisation des Données</h2>
                <p style="color: var(--color-gray); line-height: 1.8;">
                    Vos données personnelles sont utilisées pour:
                </p>
                <ul style="color: var(--color-gray); line-height: 2;">
                    <li>Traiter vos commandes</li>
                    <li>Vous envoyer des confirmations et mises à jour</li>
                    <li>Améliorer notre service</li>
                    <li>Vous contacter pour des raisons de support</li>
                </ul>

                <h2 style="color: var(--color-forest); margin-top: 2rem; margin-bottom: 1rem;">4. Sécurité des Données</h2>
                <p style="color: var(--color-gray); line-height: 1.8;">
                    Nous utilisons des mesures de sécurité avancées pour protéger vos données personnelles contre tout accès non autorisé, altération, divulgation ou destruction.
                </p>

                <h2 style="color: var(--color-forest); margin-top: 2rem; margin-bottom: 1rem;">5. Partage des Données</h2>
                <p style="color: var(--color-gray); line-height: 1.8;">
                    Nous ne partageons pas vos données personnelles avec des tiers sans votre consentement, sauf si la loi l'exige.
                </p>

                <h2 style="color: var(--color-forest); margin-top: 2rem; margin-bottom: 1rem;">6. Cookies</h2>
                <p style="color: var(--color-gray); line-height: 1.8;">
                    Ce site utilise des cookies pour améliorer votre expérience utilisateur. Vous pouvez contrôler l'utilisation des cookies via les paramètres de votre navigateur.
                </p>

                <h2 style="color: var(--color-forest); margin-top: 2rem; margin-bottom: 1rem;">7. Vos Droits</h2>
                <p style="color: var(--color-gray); line-height: 1.8;">
                    Vous avez le droit d'accéder, de modifier ou de supprimer vos données personnelles. Contactez-nous pour exercer ces droits.
                </p>

                <h2 style="color: var(--color-forest); margin-top: 2rem; margin-bottom: 1rem;">8. Contact</h2>
                <p style="color: var(--color-gray); line-height: 1.8;">
                    Si vous avez des questions concernant cette politique de confidentialité, veuillez nous contacter.
                </p>

                <div style="background: var(--color-light-gray); padding: 1.5rem; border-radius: 8px; margin-top: 2rem;">
                    <p style="margin: 0; color: var(--color-gray); font-size: 0.9rem;">
                        <strong>Nous Contacter:</strong> <a href="mailto:nathanngassai885@gmail.com" style="color: var(--color-forest);">nathanngassai885@gmail.com</a> | <a href="https://wa.me/242066817726" style="color: var(--color-forest);">WhatsApp</a>
                    </p>
                </div>
            </div>
        </div>
    </section>
</main>

<?php require_once 'includes/footer.php'; ?>
