<?php
/**
 * contact.php - Page Contact Client → Admin
 * Flux: Insert message + notification admin
 * Table messages: id_message, user_id, nom, email, telephone, objet, message, reponse, date_reponse, statut, date_creation, created_at, lu
 * Table notifications: id_notification, user_id, type, titre, message, lu, lien, date_creation
 */

$page_title = 'Contact - Sepelas&Lia';

$message = '';
$message_type = 'success';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom'] ?? '');
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $sujet = trim($_POST['sujet'] ?? '');
    $msg = trim($_POST['message'] ?? '');
    
    if ($nom && $email && $msg) {
        try {
            require_once 'config/db.php';
            
            // 1. Insérer dans la table messages (colonnes: user_id, nom, email, telephone, objet, message)
            // user_id peut être NULL si client non connecté
            $user_id = $_SESSION['user_id'] ?? null;
            
            $stmt = $pdo->prepare("
                INSERT INTO messages (user_id, nom, email, telephone, objet, message, statut, date_creation, lu) 
                VALUES (?, ?, ?, ?, ?, ?, 'nouveau', NOW(), 0)
            ");
            $stmt->execute([$user_id, $nom, $email, '', $sujet, $msg]);
            
            // 2. Créer notification pour l'admin
            // Trouver l'admin
            $admin = $pdo->query("SELECT id_user FROM utilisateurs WHERE role = 'admin' LIMIT 1");
            $admin_row = $admin->fetch();
            
            if ($admin_row) {
                $notif = $pdo->prepare("
                    INSERT INTO notifications (user_id, type, titre, message, lu, date_creation) 
                    VALUES (?, 'message', 'Nouveau message de contact', ?, 0, NOW())
                ");
                $notif->execute([$admin_row['id_user'], "Vous avez reçu un message de $nom"]);
            }
            
            $message = 'Merci pour votre message! Nous vous répondrons bientôt.';
            $message_type = 'success';
        } catch (PDOException $e) {
            $message = 'Erreur lors de l\'envoi. Veuillez réessayer.';
            $message_type = 'danger';
        }
    } else {
        $message = 'Veuillez remplir tous les champs obligatoires.';
        $message_type = 'danger';
    }
}

require_once 'includes/header.php';
?>

<main style="padding: 20px; background: #fff; min-height: 100vh;">
    <div style="max-width: 600px; margin: 0 auto;">
        <div style="background: white; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); padding: 24px;">
            <div style="text-align: center; margin-bottom: 20px;">
                <div style="font-size: 3rem;">📞</div>
                <h1 style="font-size: 1.5rem; color: #1a4d2e; margin: 0;">Contactez-nous</h1>
                <p style="color: #666;">Nous sommes disponibles pour vous aider</p>
            </div>
            
            <?php if ($message): ?>
                <div style="padding: 12px; border-radius: 8px; margin-bottom: 20px; background: <?php echo $message_type === 'success' ? '#d4edda' : '#f8d7da'; ?>; color: <?php echo $message_type === 'success' ? '#155724' : '#721c24'; ?>; border: 1px solid <?php echo $message_type === 'success' ? '#c3e6cb' : '#f5c6cb'; ?>;">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>
            
            <form method="POST">
                <div style="margin-bottom: 15px;">
                    <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #333;">Nom complet *</label>
                    <input type="text" name="nom" required 
                           style="width: 100%; padding: 12px; border: 2px solid #ddd; border-radius: 8px; font-size: 16px; box-sizing: border-box;">
                </div>
                
                <div style="margin-bottom: 15px;">
                    <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #333;">Email *</label>
                    <input type="email" name="email" required 
                           style="width: 100%; padding: 12px; border: 2px solid #ddd; border-radius: 8px; font-size: 16px; box-sizing: border-box;">
                </div>
                
                <div style="margin-bottom: 15px;">
                    <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #333;">Sujet</label>
                    <input type="text" name="sujet" 
                           style="width: 100%; padding: 12px; border: 2px solid #ddd; border-radius: 8px; font-size: 16px; box-sizing: border-box;">
                </div>
                
                <div style="margin-bottom: 15px;">
                    <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #333;">Message *</label>
                    <textarea name="message" rows="5" required 
                              style="width: 100%; padding: 12px; border: 2px solid #ddd; border-radius: 8px; font-size: 16px; box-sizing: border-box; resize: vertical; font-family: inherit;"></textarea>
                </div>
                
                <button type="submit" style="width: 100%; background: #1a4d2e; color: white; border: none; padding: 14px; border-radius: 8px; font-size: 16px; font-weight: 600; cursor: pointer;">
                    <i class="fas fa-paper-plane"></i> Envoyer mon message
                </button>
            </form>
            
            <div style="margin-top: 24px; padding-top: 16px; border-top: 1px solid #eee;">
                <h3 style="font-size: 1rem; margin-bottom: 12px; color: #333;">Autres moyens de contact</h3>
                <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                    <a href="tel:+242066817726" style="flex: 1; min-width: 150px; display: flex; align-items: center; justify-content: center; gap: 8px; padding: 12px; background: #1a4d2e; color: white; border-radius: 8px; text-decoration: none; font-weight: 600;">
                        <i class="fas fa-phone"></i> Appeler
                    </a>
                    <a href="https://wa.me/242066817726" target="_blank" 
                       style="flex: 1; min-width: 150px; display: flex; align-items: center; justify-content: center; gap: 8px; padding: 12px; background: #25D366; color: white; border-radius: 8px; text-decoration: none; font-weight: 600;">
                        <i class="fab fa-whatsapp"></i> WhatsApp
                    </a>
                </div>
            </div>
        </div>
    </div>
</main>

<style>
@media (max-width: 480px) {
    main { padding: 10px !important; }
    .card-body { padding: 16px !important; }
}
</style>

<?php require_once 'includes/footer.php'; ?>

