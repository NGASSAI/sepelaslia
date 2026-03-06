<?php
/**
 * forgot_password.php - Demande de réinitialisation de mot de passe
 * Page sécurisée pour demander un lien de réinitialisation
 */

$page_title = 'Mot de passe oublié - Sepelas&Lia';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Inclure la configuration de la base de données pour header.php
require_once 'config/db.php';

if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$error = '';
$success = '';
$show_token = false;
$reset_token = '';
$user_email = '';

// Vérifier le token pré-rempli (si venu du formulaire de reset)
$prefilled_token = $_GET['token'] ?? '';
$prefilled_email = $_GET['email'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Générer le token CSRF
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    
    // Vérifier le token CSRF
    $submitted_token = $_POST['csrf_token'] ?? '';
    if (!hash_equals($_SESSION['csrf_token'], $submitted_token)) {
        $error = 'Erreur de sécurité. Veuillez réessayer.';
    } else {
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Veuillez entrer une adresse email valide.';
        } else {
            // Inclure les fonctions de reset
            require_once 'includes/password_reset.php';
            
            // Vérifier le rate limiting
            if (!checkRateLimit($email)) {
                $error = 'Trop de demandes. Veuillez attendre 1 heure avant de réessayer.';
            } else {
                // Créer la demande de reset
                $result = createPasswordResetRequest($email);
                
                if ($result['success']) {
                    $success = $result['message'];
                    
                    // Afficher le token pour que l'utilisateur puisse le copier
                    // (Dans un vrai système, cela serait envoyé par email)
                    if (isset($result['token'])) {
                        $show_token = true;
                        $reset_token = $result['token'];
                        $user_email = $result['email'];
                    }
                } else {
                    $error = $result['message'];
                }
            }
        }
    }
}

require_once 'includes/header.php';
?>

<main>
    <div class="container" style="max-width: 450px; padding-top: 2rem;">
        <div class="card animate-fade">
            <div class="card-body">
                <div style="text-align: center; margin-bottom: 1.5rem;">
                    <div style="font-size: 2.5rem; margin-bottom: 0.5rem;">🔑</div>
                    <h1 style="font-size: 1.5rem; color: var(--color-forest);">Mot de passe oublié</h1>
                    <p class="text-muted">Entrez votre email pour réinitialiser votre mot de passe</p>
                </div>
                
                <?php if ($error): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>
                
                <?php if ($success): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($success); ?>
                    </div>
                    
                    <?php if ($show_token && !empty($reset_token)): ?>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            <strong>Copiez ce token:</strong><br>
                            <code style="word-break: break-all; font-size: 0.85rem;"><?php echo htmlspecialchars($reset_token); ?></code>
                            <br><br>
                            <a href="reset_password_form.php?token=<?php echo urlencode($reset_token); ?>" class="btn btn-primary btn-block">
                                <i fa-key></i> Définir mon nouveau mot de passe
                            </a>
                        </div>
                        
                        <div style="background: #fff3cd; padding: 1rem; border-radius: 8px; margin-top: 1rem; font-size: 0.9rem;">
                            <strong>⚠️ Important:</strong>
                            <ul style="margin: 0.5rem 0 0 1rem; padding-left: 0;">
                                <li>Ce token expire dans 1 heure</li>
                                <li>Notez-le ou copiez-le maintenant</li>
                                <li>Cliquez sur le lien ci-dessus pour continuer</li>
                            </ul>
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <form method="POST">
                        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token'] ?? ''; ?>">
                        
                        <div class="form-group">
                            <label for="email" class="form-label">Adresse email</label>
                            <input type="email" id="email" name="email" class="form-control" 
                                   value="<?php echo htmlspecialchars($prefilled_email); ?>"
                                   placeholder="votre@email.com" required>
                        </div>
                        
                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="fas fa-paper-plane"></i> Envoyer le lien de réinitialisation
                        </button>
                    </form>
                <?php endif; ?>
                
                <div style="text-align: center; margin-top: 1.5rem; padding-top: 1rem; border-top: 1px solid var(--color-border);">
                    <p class="text-muted" style="font-size: 0.9rem;">
                        <a href="login.php" style="color: var(--color-terracotta);">
                            <i class="fas fa-arrow-left"></i> Retour à la connexion
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</main>

<?php require_once 'includes/footer.php'; ?>

<?php
// Fermer la balise PHP (optionnel mais recommandé pour la cohérence)
?>

