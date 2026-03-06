<?php
/**
 * mes-messages.php - Mes Messages Client
 * Le client voit ses messages, peut envoyer et supprimer des messages
 * Table messages: id_message, user_id, nom, email, telephone, objet, message, reponse, date_reponse, statut, date_creation, lu
 */

session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$page_title = 'Mes messages';

require_once 'config/db.php';

// Marquer tous les messages comme lus quand le client ouvre la page
try {
    $stmt = $pdo->prepare("UPDATE messages SET lu = 1 WHERE user_id = ? AND lu = 0");
    $stmt->execute([$user_id]);
} catch (PDOException $e) {
    // Continue silently
}

// Traiter l'envoi de message
$message_envoye = false;
$erreur = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['envoyer_message'])) {
    $objet = trim($_POST['objet'] ?? '');
    $message_texte = trim($_POST['message'] ?? '');
    
    if (!empty($objet) && !empty($message_texte)) {
        try {
            // Récupérer les infos utilisateur
            $stmt = $pdo->prepare("SELECT nom_complet, email, telephone FROM utilisateurs WHERE id_user = ?");
            $stmt->execute([$user_id]);
            $user = $stmt->fetch();
            
            if ($user) {
                // Insérer le message
                $insert = $pdo->prepare("
                    INSERT INTO messages (user_id, nom, email, telephone, objet, message, date_creation, lu)
                    VALUES (?, ?, ?, ?, ?, ?, NOW(), 0)
                ");
                $insert->execute([
                    $user_id,
                    $user['nom_complet'],
                    $user['email'],
                    $user['telephone'],
                    $objet,
                    $message_texte
                ]);
                $message_envoye = true;
            }
        } catch (PDOException $e) {
            $erreur = 'Erreur: ' . $e->getMessage();
        }
    } else {
        $erreur = 'Veuillez remplir tous les champs';
    }
}

// Supprimer un message
if (isset($_GET['supprimer']) && is_numeric($_GET['supprimer'])) {
    $msg_id = (int)$_GET['supprimer'];
    try {
        $stmt = $pdo->prepare("DELETE FROM messages WHERE id_message = ? AND user_id = ?");
        $stmt->execute([$msg_id, $user_id]);
        header('Location: mes-messages.php');
        exit;
    } catch (PDOException $e) {}
}

// Récupérer les messages du client
$stmt = $pdo->prepare("
    SELECT * FROM messages 
    WHERE user_id = ? 
    ORDER BY date_creation DESC 
    LIMIT 50
");
$stmt->execute([$user_id]);
$messages = $stmt->fetchAll();

require_once 'includes/header.php';
?>

<main style="padding: 20px; background: #ffffff; min-height: 100vh;">
    <div style="max-width: 700px; margin: 0 auto;">
        <!-- En-tête -->
        <div style="background: #ffffff; border: 2px solid #000000; border-radius: 12px; padding: 20px; margin-bottom: 20px;">
            <h1 style="font-size: 1.5rem; color: #000000; margin: 0; font-weight: 800;">
                <i class="fas fa-envelope" style="color: #000000;"></i> Mes messages
            </h1>
            <p style="color: #333333; margin-top: 8px; font-size: 0.95rem;">
                Vos messages et les réponses de Sepelas&Lia
            </p>
        </div>
        
        <!-- Message succès -->
        <?php if ($message_envoye): ?>
            <div style="background: #dcfce7; color: #166534; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 2px solid #22c55e;">
                ✅ Message envoyé avec succès! Nous vous répondrons bientôt.
            </div>
        <?php endif; ?>
        
        <!-- Erreur -->
        <?php if ($erreur): ?>
            <div style="background: #fee2e2; color: #991b1b; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 2px solid #ef4444;">
                ❌ <?php echo htmlspecialchars($erreur); ?>
            </div>
        <?php endif; ?>
        
        <!-- Envoyer nouveau message -->
        <div style="background: #ffffff; border: 2px solid #000000; border-radius: 12px; padding: 20px; margin-bottom: 20px;">
            <h2 style="font-size: 1.1rem; color: #000000; margin: 0 0 15px 0; font-weight: 700;">
                <i class="fas fa-pen"></i> Envoyer un message
            </h2>
            <form method="POST" style="display: flex; flex-direction: column; gap: 12px;">
                <input type="text" name="objet" placeholder="Sujet" required 
                    style="padding: 12px; border: 2px solid #000000; border-radius: 8px; font-size: 1rem; width: 100%;">
                <textarea name="message" placeholder="Votre message..." rows="4" required
                    style="padding: 12px; border: 2px solid #000000; border-radius: 8px; font-size: 1rem; width: 100%; resize: vertical;"></textarea>
                <button type="submit" name="envoyer_message" style="background: #1a4d2e; color: #ffffff; border: none; padding: 14px 24px; border-radius: 8px; font-size: 1rem; font-weight: 700; cursor: pointer;">
                    <i class="fas fa-paper-plane"></i> Envoyer
                </button>
            </form>
        </div>
        
        <!-- Liste messages -->
        <?php if (empty($messages)): ?>
            <div style="background: #ffffff; border: 2px solid #000000; border-radius: 12px; padding: 60px 20px; text-align: center;">
                <div style="font-size: 4rem; color: #cccccc;"><i class="fas fa-envelope-open"></i></div>
                <h2 style="color: #000000; margin: 20px 0 10px; font-size: 1.2rem; font-weight: 700;">Aucun message</h2>
                <p style="color: #333333; font-size: 0.95rem;">Vous n'avez pas encore envoyé de message.</p>
            </div>
        <?php else: ?>
            <div style="display: flex; flex-direction: column; gap: 15px;">
                <?php foreach ($messages as $msg): 
                    $has_reponse = !empty($msg['reponse']);
                    $bg_style = $msg['lu'] ? '#f5f5f5' : '#e8f4fd';
                    $border_left = $msg['lu'] ? '4px solid #cccccc' : '4px solid #22c55e';
                ?>
                    <div style="background: <?php echo $bg_style; ?>; border-left: <?php echo $border_left; ?>; border-radius: 12px; overflow: hidden; border: 2px solid #000000;">
                        <div style="background: #1a4d2e; color: #ffffff; padding: 12px 16px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 8px;">
                            <div style="font-weight: 700;">
                                <?php echo htmlspecialchars($msg['objet'] ?: 'Sans objet'); ?>
                            </div>
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <a href="mes-messages.php?supprimer=<?php echo $msg['id_message']; ?>" 
                                   onclick="return confirm('Supprimer ce message?')"
                                   style="background: #ef4444; color: white; text-decoration: none; padding: 4px 10px; border-radius: 6px; font-size: 0.75rem; font-weight: 600;">
                                    <i class="fas fa-trash-can"></i>
                                </a>
                                <span style="font-size: 0.85rem;">
                                    <?php echo date('d/m/Y H:i', strtotime($msg['date_creation'])); ?>
                                </span>
                            </div>
                        </div>
                        
                        <div style="padding: 16px;">
                            <!-- Message du client -->
                            <div style="margin-bottom: 15px;">
                                <div style="font-weight: 700; color: #000000; margin-bottom: 6px; font-size: 0.9rem;">
                                    <i class="fas fa-user"></i> Votre message:
                                </div>
                                <div style="color: #000000; font-size: 0.95rem; line-height: 1.5; background: #ffffff; padding: 12px; border-radius: 8px; border: 1px solid #ddd;">
                                    <?php echo nl2br(htmlspecialchars($msg['message'])); ?>
                                </div>
                            </div>
                            
                            <!-- Réponse de l'admin -->
                            <?php if ($has_reponse): ?>
                                <div style="background: #dcfce7; padding: 16px; border-radius: 8px; border-left: 4px solid #22c55e;">
                                    <div style="font-weight: 700; color: #166534; margin-bottom: 8px; font-size: 0.9rem; display: flex; align-items: center; gap: 8px;">
                                        <i class="fas fa-reply"></i> Réponse de Sepelas&Lia:
                                    </div>
                                    <div style="color: #000000; font-size: 0.95rem; line-height: 1.5;">
                                        <?php echo nl2br(htmlspecialchars($msg['reponse'])); ?>
                                    </div>
                                    <?php if (!empty($msg['date_reponse'])): ?>
                                        <div style="font-size: 0.8rem; color: #666666; margin-top: 8px;">
                                            Répondu le <?php echo date('d/m/Y à H:i', strtotime($msg['date_reponse'])); ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php else: ?>
                                <div style="background: #fef3c7; padding: 12px; border-radius: 8px; display: flex; align-items: center; gap: 8px;">
                                    <i class="fas fa-clock" style="color: #92400e;"></i>
                                    <span style="color: #92400e; font-size: 0.9rem; font-weight: 600;">En attente de réponse...</span>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <!-- Auto-refresh toutes les 10 secondes pour vérifier les nouvelles réponses -->
            <script>
            setTimeout(function() {
                location.reload();
            }, 10000);
            </script>
        <?php endif; ?>
        
        <!-- Retour -->
        <div style="margin-top: 20px; text-align: center;">
            <a href="index.php" style="background: #000000; color: #ffffff; text-decoration: none; padding: 14px 28px; border-radius: 10px; display: inline-block; font-weight: 700; font-size: 1rem; border: 2px solid #000000;">
                <i class="fas fa-arrow-left"></i> Retour à l'accueil
            </a>
        </div>
</main>

<style>
@media (max-width: 480px) {
    main { padding: 10px !important; }
    div[style*="padding: 16px"] { flex-direction: column !important; }
}
</style>

<?php require_once 'includes/footer.php'; ?>

