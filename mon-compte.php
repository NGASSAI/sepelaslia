<?php
require_once 'config/db.php';
$page_title = 'Mon Compte - Sepelas&Lia';
session_start();
if (!isset($_SESSION['user_id'])) { header('Location: login.php'); exit; }

$user_id = $_SESSION['user_id'];
$notifications = [];
try { 
    $stmt = $pdo->prepare("SELECT * FROM notifications WHERE user_id = ? ORDER BY date_creation DESC LIMIT 10");
    $stmt->execute([$user_id]);
    $notifications = $stmt->fetchAll(); 
} catch (PDOException $e) {}

$nb_non_lues = 0;
try { 
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM notifications WHERE user_id = ? AND lu = 0");
    $stmt->execute([$user_id]);
    $nb_non_lues = $stmt->fetchColumn();
} catch (PDOException $e) {}

// Traitement du changement de mot de passe
$password_error = '';
$password_success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'change_password') {
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
        $password_error = 'Veuillez remplir tous les champs.';
    } elseif (strlen($new_password) < 6) {
        $password_error = 'Le nouveau mot de passe doit contenir au moins 6 caractères.';
    } elseif ($new_password !== $confirm_password) {
        $password_error = 'Les mots de passe ne correspondent pas.';
    } else {
        try {
            // Vérifier l'ancien mot de passe
            $stmt = $pdo->prepare("SELECT mot_de_passe FROM utilisateurs WHERE id_user = ?");
            $stmt->execute([$user_id]);
            $user = $stmt->fetch();
            
            if ($user && password_verify($current_password, $user['mot_de_passe'])) {
                // Mettre à jour le mot de passe
                $new_hash = password_hash($new_password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE utilisateurs SET mot_de_passe = ? WHERE id_user = ?");
                $stmt->execute([$new_hash, $user_id]);
                $password_success = 'Mot de passe modifié avec succès!';
            } else {
                $password_error = 'Mot de passe actuel incorrect.';
            }
        } catch (PDOException $e) {
            $password_error = 'Erreur lors de la modification du mot de passe.';
        }
    }
}

require_once 'includes/header.php';
?>

<style>
.tabs { display: flex; border-bottom: 2px solid #e5e7eb; margin-bottom: 1.5rem; overflow-x: auto; }
.tab { padding: 1rem 1.5rem; cursor: pointer; color: #6b7280; border-bottom: 2px solid transparent; white-space: nowrap; }
.tab.active { color: var(--color-forest); border-bottom-color: var(--color-forest); font-weight: 600; }
.tab-content { display: none; }
.tab-content.active { display: block; }
.notif-item { display: flex; gap: 1rem; padding: 1rem; border-bottom: 1px solid #e5e7eb; }
.notif-item:hover { background: #f9fafb; }
.notif-icon { width: 40px; height: 40px; border-radius: 50%; background: #e5e7eb; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.notif-content { flex: 1; min-width: 0; }
.notif-time { font-size: 0.75rem; color: #9ca3af; }
.badge-pulse { animation: pulse 1.5s infinite; }
@keyframes pulse { 0%, 100% { opacity: 1; } 50% { opacity: 0.5; } }
.password-form { background: #f9fafb; padding: 1.5rem; border-radius: 8px; }
.password-form .form-group { margin-bottom: 1rem; }
.password-form label { display: block; margin-bottom: 0.5rem; font-weight: 500; }
.password-form input { width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 6px; font-size: 1rem; }
.password-form input:focus { outline: none; border-color: var(--color-forest); box-shadow: 0 0 0 3px rgba(34, 90, 59, 0.1); }
</style>

<main>
<div class="container" style="max-width: 800px; padding: 2rem 1rem;">

<div class="tabs">
    <div class="tab active" onclick="switchTab(this, 'infos')">📋 Mes Infos</div>
    <div class="tab" onclick="switchTab(this, 'commandes')">📦 Mes Commandes</div>
    <div class="tab" onclick="switchTab(this, 'notifs')">
        🔔 Notifications
        <?php if($nb_non_lues > 0): ?><span class="badge-pulse" style="background:#ef4444;color:white;padding:0.1rem 0.4rem;border-radius:10px;font-size:0.7rem;margin-left:0.3rem;"><?php echo $nb_non_lues; ?></span><?php endif; ?>
    </div>
    <div class="tab" onclick="switchTab(this, 'securite')">🔐 Sécurité</div>
</div>

<div id="infos" class="tab-content active">
<h2>Mes Informations</h2>
<p>Bienvenue, <strong><?php echo htmlspecialchars($_SESSION['user_nom'] ?? 'Client'); ?></strong>!</p>
<p>Email: <?php echo htmlspecialchars($_SESSION['user_email'] ?? 'Non défini'); ?></p>
<p>Téléphone: <?php echo htmlspecialchars($_SESSION['user_telephone'] ?? 'Non défini'); ?></p>
</div>

<div id="commandes" class="tab-content">
<h2>Mes Commandes</h2>
<p><a href="mes-commandes.php" style="color:var(--color-forest);">Voir mes commandes →</a></p>
</div>

<div id="notifs" class="tab-content">
<h2>Mes Notifications</h2>
<?php if(empty($notifications)): ?><p style="color:#6b7280;">Aucune notification</p>
<?php else: foreach($notifications as $notif): ?>
<div class="notif-item">
    <div class="notif-icon"><?php echo $notif['type']==='commande'?'📦':'💬'; ?></div>
    <div class="notif-content">
        <strong><?php echo htmlspecialchars($notif['titre']); ?></strong>
        <p style="margin:0.25rem 0;color:#4b5563;"><?php echo htmlspecialchars($notif['message']); ?></p>
        <span class="notif-time"><?php echo date('d/m/Y H:i', strtotime($notif['date_creation'])); ?></span>
    </div>
</div>
<?php endforeach; endif; ?>
</div>

<div id="securite" class="tab-content">
<h2>Sécurité du compte</h2>
<p>Gérez la sécurité de votre mot de passe</p>

<?php if($password_error): ?>
<div class="alert alert-danger" style="background:#fef2f2;border-color:#fecaca;color:#991b1b;padding:1rem;border-radius:8px;margin-bottom:1rem;">
    <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($password_error); ?>
</div>
<?php endif; ?>

<?php if($password_success): ?>
<div class="alert alert-success" style="background:#f0fdf4;border-color:#bbf7d0;color:#166534;padding:1rem;border-radius:8px;margin-bottom:1rem;">
    <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($password_success); ?>
</div>
<?php endif; ?>

<div class="password-form">
    <h3 style="margin-bottom:1rem;color:var(--color-forest);">🔑 Changer mon mot de passe</h3>
    <form method="POST">
        <input type="hidden" name="action" value="change_password">
        
        <div class="form-group">
            <label for="current_password">Mot de passe actuel</label>
            <input type="password" id="current_password" name="current_password" required>
        </div>
        
        <div class="form-group">
            <label for="new_password">Nouveau mot de passe</label>
            <input type="password" id="new_password" name="new_password" minlength="6" required>
            <small style="color:#6b7280;">Minimum 6 caractères</small>
        </div>
        
        <div class="form-group">
            <label for="confirm_password">Confirmer le nouveau mot de passe</label>
            <input type="password" id="confirm_password" name="confirm_password" required>
        </div>
        
        <button type="submit" class="btn btn-primary" style="background:var(--color-forest);color:white;padding:0.75rem 1.5rem;border:none;border-radius:6px;cursor:pointer;font-size:1rem;">
            <i class="fas fa-save"></i> Enregistrer le nouveau mot de passe
        </button>
    </form>
</div>

<div style="margin-top:1.5rem;padding:1rem;background:#fff3cd;border-radius:8px;">
    <strong>💡 Conseils de sécurité:</strong>
    <ul style="margin:0.5rem 0 0 1rem;color:#92400e;">
        <li>Utilisez un mot de passe d'au moins 8 caractères</li>
        <li>Combinez lettres, chiffres et caractères spéciaux</li>
        <li>Ne partagez jamais votre mot de passe</li>
    </ul>
</div>

<p style="margin-top:1.5rem;">
    <a href="forgot_password.php" style="color:var(--color-terracotta);">
        <i class="fas fa-key"></i> Mot de passe oublié ?
    </a>
</p>
</div>

</div>
</main>

<script>
function switchTab(el, tabId) {
    document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
    document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
    el.classList.add('active');
    document.getElementById(tabId).classList.add('active');
}
</script>

<?php require_once 'includes/footer.php'; ?>

