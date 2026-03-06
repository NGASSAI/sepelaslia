<?php
/**
 * panier.php - Gestion du panier
 */

$page_title = 'Panier - Sepelas&Lia';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Initialiser le panier
if (!isset($_SESSION['panier'])) {
    $_SESSION['panier'] = [];
}

// Ajouter un produit au panier
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajouter_panier'])) {
    $produit_id = (int)$_POST['produit_id'];
    $quantite = (int)$_POST['quantite'];
    
    if ($quantite > 0) {
        if (isset($_SESSION['panier'][$produit_id])) {
            $_SESSION['panier'][$produit_id] += $quantite;
        } else {
            $_SESSION['panier'][$produit_id] = $quantite;
        }
    }
    
    header('Location: panier.php');
    exit;
}

// Supprimer du panier
if (isset($_GET['supprimer'])) {
    $id = (int)$_GET['supprimer'];
    unset($_SESSION['panier'][$id]);
    header('Location: panier.php');
    exit;
}

require_once 'config/db.php';

// Récupérer les produits du panier
$produits_panier = [];
$total = 0;

if (!empty($_SESSION['panier'])) {
    $ids = array_keys($_SESSION['panier']);
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    
    $stmt = $pdo->prepare("SELECT * FROM produits WHERE id_produit IN ($placeholders)");
    $stmt->execute($ids);
    $produits = $stmt->fetchAll();
    
    foreach ($produits as $produit) {
        $qty = (int)($_SESSION['panier'][$produit['id_produit']] ?? 0);
        if ($qty <= 0) continue;
        $produit['quantite'] = $qty;
        $produit['sous_total'] = $produit['prix'] * $qty;
        $total += $produit['sous_total'];
        $produits_panier[] = $produit;
    }
}

// Points de vente pour retrait
$points_vente = [];
try {
    $stmt = $pdo->query("SELECT * FROM points_de_vente WHERE actif = 1 ORDER BY nom_pdv");
    $points_vente = $stmt->fetchAll();
} catch (Exception $e) {}

require_once 'includes/header.php';
?>

<main>
    <div class="container">
        <h1 style="margin-bottom: 1.5rem; font-size: 1.5rem;">
            <i class="fas fa-shopping-cart"></i> Mon Panier
        </h1>

        <?php if (empty($produits_panier)): ?>
            <div class="card">
                <div class="card-body text-center">
                    <div style="font-size: 3rem; margin-bottom: 1rem;">🛒</div>
                    <p class="text-muted">Votre panier est vide</p>
                    <a href="produit.php" class="btn btn-primary" style="margin-top: 1rem;">
                        Voir les produits
                    </a>
                </div>
            </div>
        <?php else: ?>
            <div class="card" style="margin-bottom: 1.5rem;">
                <div class="card-body" style="padding: 0.75rem;">
                    <?php foreach ($produits_panier as $produit): ?>
                        <?php
                        $rawImage = (string)($produit['image'] ?? '');
                        $imageName = $rawImage ? basename(str_replace('\\', '/', $rawImage)) : 'placeholder.jpg';
                        $imageSrc = 'uploads/' . $imageName;
                        ?>
                        <div class="cart-item">
                            <img
                                src="<?php echo htmlspecialchars($imageSrc); ?>"
                                data-lightbox="<?php echo htmlspecialchars($imageSrc); ?>"
                                alt="<?php echo htmlspecialchars($produit['nom']); ?>"
                                class="cart-item-image"
                                onerror="this.src='https://via.placeholder.com/80?text=P'">
                            <div class="cart-item-info">
                                <div class="cart-item-name"><?php echo htmlspecialchars($produit['nom']); ?></div>
                                <div class="text-muted" style="font-size: 0.9rem;">
                                    <?php echo (int)$produit['quantite']; ?> × <?php echo number_format((int)$produit['prix'], 0, ',', ' '); ?> F
                                </div>
                                <div class="cart-item-price">
                                    <?php echo number_format((int)$produit['sous_total'], 0, ',', ' '); ?> F
                                </div>
                            </div>
                            <div style="display: flex; flex-direction: column; align-items: flex-end; gap: 0.5rem;">
                                <a href="panier.php?supprimer=<?php echo (int)$produit['id_produit']; ?>"
                                   style="color: var(--color-danger); font-size: 0.85rem;"
                                   onclick="return confirm('Supprimer ce produit ?')">
                                    Supprimer
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>

                    <div style="padding: 1rem; background: var(--color-light-gray); border-radius: 12px; display: flex; justify-content: space-between; align-items: center;">
                        <span style="font-weight: 700;">Total</span>
                        <span style="font-size: 1.5rem; font-weight: 800; color: var(--color-forest);">
                            <?php echo number_format((int)$total, 0, ',', ' '); ?> F
                        </span>
                    </div>
                </div>
            </div>

            <?php if (isset($_SESSION['user_id'])): ?>
                <div class="card">
                    <div class="card-body">
                        <h2 style="font-size: 1.25rem; margin-bottom: 1rem;">Finaliser la commande</h2>

                        <form method="POST" action="checkout.php">
                            <input type="hidden" name="total" value="<?php echo (int)$total; ?>">

                            <div class="form-group">
                                <label for="point_vente" class="form-label">Point de retrait *</label>
                                <select id="point_vente" name="point_vente" class="form-control" required>
                                    <option value="">Sélectionner un point</option>
                                    <?php foreach ($points_vente as $pdv): ?>
                                        <option value="<?php echo (int)$pdv['id_pdv']; ?>">
                                            <?php echo htmlspecialchars($pdv['nom_pdv']); ?> — <?php echo htmlspecialchars($pdv['adresse_pdv']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="paiement" class="form-label">Mode de paiement *</label>
                                <select id="paiement" name="paiement" class="form-control" required>
                                    <option value="">Sélectionner</option>
                                    <option value="mobile_money">📱 Mobile Money</option>
                                    <option value="livraison">💵 Paiement à la livraison</option>
                                </select>
                            </div>

                            <button type="submit" class="btn btn-primary btn-block" style="margin-top: 1rem;">
                                <i class="fas fa-check"></i> Passer la commande
                            </button>
                        </form>
                    </div>
                </div>
            <?php else: ?>
                <div class="card">
                    <div class="card-body text-center">
                        <p>Veuillez vous connecter pour finaliser votre commande.</p>
                        <a href="login.php" class="btn btn-primary">Se connecter</a>
                    </div>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</main>

<?php require_once 'includes/footer.php'; ?>
