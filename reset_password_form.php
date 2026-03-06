<?php
/**
 * reset_password_form.php - Formulaire de nouveau mot de passe
 * Page sécurisée pour définir un nouveau mot de passe après vérification du token
 */

$page_title = 'Nouveau mot de passe - Sepelas&Lia';

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
$valid_token = false;
$user_id = null;

// Récupérer le token depuis l'URL
$token = $_GET['token'] ?? '';

if (empty($token)) {
    $error = 'Token manquant. Veuillez demander un nouveau lien de réinitialisation.';
} else {
    // Valider le token
    require_once 'includes/password_reset.php';
    $validation = validateResetToken($token);
    
    if ($validation['valid']) {
        $valid_token = true;
        $user_id = $validation['user_id'];
    } else {
        $error = $validation['message'];
    }
}

// Traiter le formulaire de nouveau mot de passe
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $valid_token) {
    // Générer le token CSRF
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    
    // Vérifier le token CSRF
    $submitted_token = $_POST['csrf_token'] ?? '';
    if (!hash_equals($_SESSION['csrf_token'], $submitted_token)) {
        $error = 'Erreur de sécurité. Veuillez réessayer.';
    } else {
        $new_password = $_POST['new_password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';
        
        // Validation
        if (empty($new_password) || empty($confirm_password)) {
            $error = 'Veuillez remplir tous les champs.';
        } elseif (strlen($new_password) < 6) {
            $error = 'Le mot de passe doit contenir au moins 6 caractères.';
        } elseif ($new_password !== $confirm_password) {
            $error = 'Les mots de passe ne correspondent pas.';
        } else {
            // Réinitialiser le mot de passe
            $result = resetPassword($user_id, $new_password);
            
            if ($result['success']) {
                $success = $result['message'] . ' <a href="login.php">Cliquez ici pour vous connecter</a>';
                $valid_token = false; // Empêcher une nouvelle soumission
            } else {
                $error = $result['message'];
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
                    <div style="font-size: 2.5rem; margin-bottom: 0.5rem;">🔐</div>
                    <h1 style="font-size: 1.5rem; color: var(--color-forest);">
                        <?php echo $valid_token ? 'Nouveau mot de passe' : 'Réinitialisation'; ?>
                    </h1>
                    <p class="text-muted">
                        <?php echo $valid_token ? 'Définissez votre nouveau mot de passe' : 'Lien de réinitialisation invalide ou expiré'; ?>
                    </p>
                </div>
                
                <?php if ($error): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>
                
                <?php if ($success): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i> <?php echo $success; ?>
                    </div>
                <?php elseif ($valid_token): ?>
                    <form method="POST">
                        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token'] ?? ''; ?>">
                        
                        <div class="form-group">
                            <label for="new_password" class="form-label">Nouveau mot de passe</label>
                            <input type="password" id="new_password" name="new_password" 
                                   class="form-control" minlength="6" required>
                            <small class="text-muted">Minimum 6 caractères</small>
                        </div>
                        
                        <div class="form-group">
                            <label for="confirm_password" class="form-label">Confirmer le mot de passe</label>
                            <input type="password" id="confirm_password" name="confirm_password" 
                                   class="form-control" required>
                        </div>
                        
                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="fas fa-save"></i> Enregistrer le nouveau mot de passe
                        </button>
                    </form>
                <?php endif; ?>
                
                <div style="text-align: center; margin-top: 1.5rem; padding-top: 1rem; border-top: 1px solid var(--color-border);">
                    <p class="text-muted" style="font-size: 0.9rem;">
                        <a href="forgot_password.php" style="color: var(--color-terracotta);">
                            <i class="fas fa-key"></i> Demander un nouveau lien
                        </a>
                    </p>
                    <p class="text-muted" style="font-size: 0.9rem; margin-top: 0.5rem;">
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
// Fermer la balise PHP
?>

