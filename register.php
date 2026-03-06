<?php
/**
 * register.php - Inscription utilisateur
 */

$page_title = 'Inscription - Sepelas&Lia';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom'] ?? '');
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $telephone = trim($_POST['telephone'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';
    
    if (empty($nom) || empty($email) || empty($telephone) || empty($password)) {
        $error = 'Veuillez remplir tous les champs.';
    } elseif ($password !== $confirm) {
        $error = 'Les mots de passe ne correspondent pas.';
    } elseif (strlen($password) < 6) {
        $error = 'Le mot de passe doit contenir au moins 6 caractères.';
    } else {
        try {
            require_once 'config/db.php';
            
            // Vérifier si email existe
            $stmt = $pdo->prepare("SELECT id_user FROM utilisateurs WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                $error = 'Cet email est déjà utilisé.';
            } else {
                // Créer utilisateur
                $hashed = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("INSERT INTO utilisateurs (nom_complet, email, telephone, mot_de_passe, role, date_inscription) VALUES (?, ?, ?, ?, 'client', NOW())");
                $stmt->execute([$nom, $email, $telephone, $hashed]);
                
                $success = 'Compte créé avec succès! Vous pouvez maintenant vous connecter.';
            }
        } catch (PDOException $e) {
            $error = 'Erreur système. Veuillez réessayer.';
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
                    <div style="font-size: 2.5rem; margin-bottom: 0.5rem;">📝</div>
                    <h1 style="font-size: 1.5rem; color: var(--color-forest);">Créer un compte</h1>
                    <p class="text-muted">Rejoignez Sepelas&Lia</p>
                </div>
                
                <?php if ($error): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>
                
                <?php if ($success): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($success); ?>
                        <br><a href="login.php" class="btn btn-primary btn-block" style="margin-top: 1rem;">Se connecter</a>
                    </div>
                <?php else: ?>
                    <form method="POST">
                        <div class="form-group">
                            <label for="nom" class="form-label">Nom complet</label>
                            <input type="text" id="nom" name="nom" class="form-control" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" id="email" name="email" class="form-control" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="telephone" class="form-label">Téléphone (+242)</label>
                            <input type="tel" id="telephone" name="telephone" class="form-control" placeholder="06 6xx xxx xx" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="password" class="form-label">Mot de passe</label>
                            <input type="password" id="password" name="password" class="form-control" minlength="6" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="confirm_password" class="form-label">Confirmer le mot de passe</label>
                            <input type="password" id="confirm_password" name="confirm_password" class="form-control" required>
                        </div>
                        
                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="fas fa-user-plus"></i> Créer mon compte
                        </button>
                    </form>
                <?php endif; ?>
                
                <div style="text-align: center; margin-top: 1.5rem;">
                    <p class="text-muted" style="font-size: 0.9rem;">Déjà inscrit? <a href="login.php" style="color: var(--color-terracotta);">Se connecter</a></p>
                </div>
            </div>
        </div>
    </div>
</main>

<?php require_once 'includes/footer.php'; ?>

