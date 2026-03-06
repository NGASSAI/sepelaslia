<?php
/**
 * login.php - Connexion utilisateur
 */

// Démarrer la session en PREMIÈRE LIGNE
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$page_title = 'Connexion - Sepelas&Lia';

if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$error = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'] ?? '';
    
    if (empty($email) || empty($password)) {
        $error = 'Veuillez remplir tous les champs.';
    } else {
        try {
            require_once 'config/db.php';
            
            $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE email = ? LIMIT 1");
            $stmt->execute([$email]);
            $user = $stmt->fetch();
            
            if ($user && password_verify($password, $user['mot_de_passe'])) {
                // Connexion réussie
                $_SESSION['user_id'] = $user['id_user'];
                $_SESSION['user_nom'] = $user['nom_complet'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_telephone'] = $user['telephone'] ?? '';
                $_SESSION['user_role'] = $user['role'] ?? 'client';
                
                // Redirection vers la page d'accueil pour tous les utilisateurs
                header('Location: index.php');
                exit;
            } else {
                $error = 'Email ou mot de passe incorrect.';
            }
        } catch (PDOException $e) {
            $error = 'Erreur système. Veuillez réessayer.';
        }
    }
}

require_once 'includes/header.php';
?>

<main>
    <div class="container" style="max-width: 400px; padding-top: 2rem;">
        <div class="card animate-fade">
            <div class="card-body">
                <div style="text-align: center; margin-bottom: 1.5rem;">
                    <div style="font-size: 2.5rem; margin-bottom: 0.5rem;">🔐</div>
                    <h1 style="font-size: 1.5rem; color: var(--color-forest);">Connexion</h1>
                    <p class="text-muted">Accédez à votre compte</p>
                </div>
                
                <?php if ($error): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST">
                    <div class="form-group">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" id="email" name="email" class="form-control" 
                               value="<?php echo htmlspecialchars($email); ?>" 
                               placeholder="votre@email.com" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="password" class="form-label">Mot de passe</label>
                        <input type="password" id="password" name="password" class="form-control" 
                               placeholder="••••••••" required>
                    </div>
                    
                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-sign-in-alt"></i> Se connecter
                    </button>
                </form>
                
                <div style="text-align: center; margin-top: 1rem;">
                    <a href="forgot_password.php" style="color: var(--color-terracotta); font-size: 0.9rem;">
                        <i class="fas fa-key"></i> Mot de passe oublié ?
                    </a>
                </div>
                
                <div style="text-align: center; margin-top: 1.5rem; padding-top: 1rem; border-top: 1px solid var(--color-border);">
                    <p class="text-muted" style="font-size: 0.9rem;">Pas de compte?</p>
                    <a href="register.php" class="btn btn-secondary btn-block" style="margin-top: 0.5rem;">
                        Créer un compte
                    </a>
                </div>
            </div>
        </div>
    </div>
</main>

<?php require_once 'includes/footer.php'; ?>

<?php
// Close PHP tag
?>

