<?php
/**
 * checkout.php - Traitement de la commande
 */

$page_title = 'Commande - Sepelas&Lia';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Vérifier si connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php?redirect=panier.php');
    exit;
}

// Vérifier si panier vide
if (empty($_SESSION['panier'])) {
    header('Location: panier.php');
    exit;
}

$error = '';
$success = false;

require_once 'config/db.php';

// Traiter le formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $point_vente = (int)$_POST['point_vente'];
    $paiement = $_POST['paiement'];
    $total = (int)$_POST['total'];
    
    if (empty($point_vente) || empty($paiement)) {
        $error = 'Veuillez sélectionner un point de retrait et un mode de paiement.';
    } else {
        try {
            $pdo->beginTransaction();
            
            // Générer numéro de commande
            $numero = 'CMD-' . date('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
            
            // Insérer commande
            $stmt = $pdo->prepare("
                INSERT INTO commandes (id_user, point_de_vente_id, date_commande, date_creation, total_montant, statut, methode_paiement, nom_client, telephone_client, email_client, numero_commande)
                VALUES (?, ?, NOW(), NOW(), ?, 'en_attente', ?, ?, ?, ?, ?)
            ");
            
            $nom = $_SESSION['user_nom'] ?? 'Client';
            $tel = $_SESSION['user_telephone'] ?? '';
            $email = $_SESSION['user_email'] ?? '';
            
            $stmt->execute([
                $_SESSION['user_id'],
                $point_vente,
                $total,
                $paiement,
                $nom,
                $tel,
                $email,
                $numero
            ]);
            
            $commande_id = $pdo->lastInsertId();
            
            // Insérer les détails
            $ids = array_keys($_SESSION['panier']);
            $placeholders = implode(',', array_fill(0, count($ids), '?'));
            $stmt = $pdo->prepare("SELECT * FROM produits WHERE id_produit IN ($placeholders)");
            $stmt->execute($ids);
            $produits = $stmt->fetchAll();
            
            foreach ($produits as $produit) {
                $qty = (int)($_SESSION['panier'][$produit['id_produit']] ?? 0);
                if ($qty <= 0) continue;
                $sous_total = $produit['prix'] * $qty;
                
                $detail = $pdo->prepare("
                    INSERT INTO details_commande (id_commande, id_produit, quantite, prix_unitaire, sous_total)
                    VALUES (?, ?, ?, ?, ?)
                ");
                $detail->execute([$commande_id, $produit['id_produit'], $qty, $produit['prix'], $sous_total]);
            }
            
            $pdo->commit();
            
            // Vider le panier
            $_SESSION['panier'] = [];
            
            // Créer notification pour le CLIENT (sans bloquer si erreur)
            try {
                $notif = $pdo->prepare("
                    INSERT INTO notifications (user_id, titre, message, type)
                    VALUES (?, ?, ?, 'commande')
                ");
                $notif->execute([
                    $_SESSION['user_id'],
                    'Commande confirmée!',
                    "Votre commande $numero a été enregistrée. Total: " . number_format($total, 0, ',', ' ') . " FCA"
                ]);
            } catch (Exception $e) {
                // Notification échouée, on continue
                error_log('Notification client échouée: ' . $e->getMessage());
            }
            
            // Créer notification pour l'ADMIN (nouvelle commande) - sans bloquer si erreur
            try {
                // Récupérer l'ID de l'admin
                $stmt_admin = $pdo->query("SELECT id_user FROM utilisateurs WHERE role = 'admin' LIMIT 1");
                $admin = $stmt_admin->fetch();
                if ($admin) {
                    $notif_admin = $pdo->prepare("
                        INSERT INTO notifications (user_id, titre, message, type)
                        VALUES (?, ?, ?, 'nouvelle_commande')
                    ");
                    $notif_admin->execute([
                        $admin['id_user'],
                        '🛒 Nouvelle commande!',
                        "Commande $numero de $nom ($tel) - Total: " . number_format($total, 0, ',', ' ') . " FCA"
                    ]);
                }
            } catch (Exception $e) {
                // Notification admin échouée, on continue
                error_log('Notification admin échouée: ' . $e->getMessage());
            }
            
            // Redirer vers merci
            header('Location: merci.php?ref=' . $numero);
            exit;
            
        } catch (Exception $e) {
            $pdo->rollBack();
            $error = 'Erreur: ' . $e->getMessage();
        }
    }
}

// Points de vente
$points_vente = [];
try {
    $stmt = $pdo->query("SELECT * FROM points_de_vente WHERE actif = 1 ORDER BY nom_pdv");
    $points_vente = $stmt->fetchAll();
} catch (Exception $e) {}

require_once 'includes/header.php';
?>

<main>
    <div class="container" style="max-width: 600px;">
        <div class="card animate-fade">
            <div class="card-body">
                <div style="text-align: center; margin-bottom: 1.5rem;">
                    <div style="font-size: 2.5rem;">📦</div>
                    <h1 style="font-size: 1.5rem; color: var(--color-forest);">Finaliser la commande</h1>
                </div>
                
                <?php if ($error): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST">
                    <div class="form-group">
                        <label class="form-label">Point de retrait *</label>
                        <select name="point_vente" class="form-control" required>
                            <option value="">Sélectionner un point</option>
                            <?php foreach ($points_vente as $pdv): ?>
                                <option value="<?php echo $pdv['id_pdv']; ?>">
                                    <?php echo htmlspecialchars($pdv['nom_pdv']); ?> - <?php echo htmlspecialchars($pdv['adresse_pdv']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Mode de paiement *</label>
                        <select name="paiement" class="form-control" required>
                            <option value="">Sélectionner</option>
                            <option value="mobile_money">📱 Mobile Money</option>
                            <option value="livraison">💵 Paiement à la livraison</option>
                        </select>
                    </div>
                    
                    <?php 
                    $total = 0;
                    if (!empty($_SESSION['panier'])) {
                        $ids = array_keys($_SESSION['panier']);
                        $placeholders = implode(',', array_fill(0, count($ids), '?'));
                        $stmt = $pdo->prepare("SELECT * FROM produits WHERE id_produit IN ($placeholders)");
                        $stmt->execute($ids);
                        foreach ($stmt->fetchAll() as $p) {
                            $qty = (int)($_SESSION['panier'][$p['id_produit']] ?? 0);
                            if ($qty <= 0) continue;
                            $total += $p['prix'] * $qty;
                        }
                    }
                    ?>
                    <input type="hidden" name="total" value="<?php echo $total; ?>">
                    
                    <div style="background: var(--color-light-gray); padding: 1rem; border-radius: 8px; margin: 1.5rem 0;">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span style="font-weight: 600;">Total à payer</span>
                            <span style="font-size: 1.5rem; font-weight: 700; color: var(--color-forest);">
                                <?php echo number_format($total, 0, ',', ' '); ?> F
                            </span>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-check"></i> Confirmer la commande
                    </button>
                    
                    <a href="panier.php" style="display: block; text-align: center; margin-top: 1rem; color: var(--color-gray);">
                        ← Retour au panier
                    </a>
                </form>
            </div>
        </div>
    </div>
</main>

<?php require_once 'includes/footer.php'; ?>
