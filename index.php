<?php
/**
 * index.php - Page d'accueil Sepelas&Lia
 * Design moderne et vivant
 */

$page_title = 'Sepelas&Lia - Produits Naturels';

// Connexion à la base
require_once 'config/db.php';

$produits = [];
try {
    // Schéma réel: id_produit, en_vedette, date_ajout - inclure tous les produits en vedette
    $stmt = $pdo->query("SELECT * FROM produits WHERE en_vedette = 1 ORDER BY stock DESC, date_ajout DESC LIMIT 8");
    $produits = $stmt->fetchAll();
} catch (PDOException $e) {
    $produits = [];
}

// Charger les points de vente
$points_vente = [];
try {
    $stmt = $pdo->query("SELECT * FROM points_de_vente ORDER BY nom_pdv");
    $points_vente = $stmt->fetchAll();
} catch (PDOException $e) {
    $points_vente = [];
}

require_once 'includes/header.php';
?>

<main>
<!-- Hero Section -->
<section class="hero">
    <h1>Bienvenue chez <span>Sepelas&Lia</span></h1>
    <p>Produits naturels et biologiques pour votre bien-être</p>
    <div style="margin-top: 1.5rem;">
        <a href="produit.php" class="btn btn-secondary">Découvrir nos produits</a>
    </div>
</section>

<!-- Services -->
<section style="padding: 2rem 0;">
    <div class="container">
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon primary">
                    <i class="fas fa-leaf"></i>
                </div>
                <div>
                    <div class="stat-value" style="font-size: 1.2rem;">100%</div>
                    <div class="stat-label">Naturel</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon secondary">
                    <i class="fas fa-shipping-fast"></i>
                </div>
                <div>
                    <div class="stat-value" style="font-size: 1.2rem;">24h</div>
                    <div class="stat-label">Livraison</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon success">
                    <i class="fas fa-store"></i>
                </div>
                <div>
                    <div class="stat-value" style="font-size: 1.2rem;"><?php echo count($points_vente); ?></div>
                    <div class="stat-label">Points retrait</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon info">
                    <i class="fas fa-headset"></i>
                </div>
                <div>
                    <div class="stat-value" style="font-size: 1.2rem;">7j/7</div>
                    <div class="stat-label">Support</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Produits en vedette -->
<section style="padding: 2rem 0;">
    <div class="container">
        <h2 class="section-title">
            <i class="fas fa-star"></i> Nos Produits
        </h2>
        
        <?php if (empty($produits)): ?>
            <div class="card">
                <div class="card-body text-center">
                    <i class="fas fa-box-open" style="font-size: 3rem; color: var(--color-gray); margin-bottom: 1rem; display: block;"></i>
                    <p class="text-muted">Aucun produit disponible pour le moment.</p>
                    <a href="produit.php" class="btn btn-primary" style="margin-top: 1rem;">Voir tous les produits</a>
                </div>
            </div>
        <?php else: ?>
            <div class="products-grid">
                <?php foreach ($produits as $produit): ?>
                    <div class="product-card">
                        <?php
                        $rawImage = (string)($produit['image'] ?? '');
                        $imageName = $rawImage ? basename(str_replace('\\', '/', $rawImage)) : 'placeholder.jpg';
                        $imageSrc = 'uploads/' . $imageName;
                        $stock = (int)($produit['stock'] ?? 0);
                        $stockClass = $stock > 5 ? 'available' : ($stock > 0 ? 'low' : 'out');
                        $stockText = $stock > 0 ? ($stock > 5 ? 'En stock' : 'Stock faible') : 'Rupture';
                        ?>
                        <a href="produit.php?id=<?php echo (int)$produit['id_produit']; ?>">
                            <img src="<?php echo htmlspecialchars($imageSrc); ?>"
                                 data-lightbox="<?php echo htmlspecialchars($imageSrc); ?>"
                                 alt="<?php echo htmlspecialchars($produit['nom']); ?>"
                                 class="product-image"
                                 onerror="this.src='https://via.placeholder.com/300x200?text=Produit'">
                        </a>
                        <div class="product-info">
                            <a href="produit.php?id=<?php echo (int)$produit['id_produit']; ?>" style="color: inherit; text-decoration: none;">
                                <h3 class="product-name"><?php echo htmlspecialchars($produit['nom']); ?></h3>
                            </a>
                            <?php if (!empty($produit['description'])): ?>
                                <p style="font-size: 0.85rem; color: var(--color-gray); margin-bottom: 0.5rem; line-height: 1.4;">
                                    <?php echo htmlspecialchars(substr($produit['description'], 0, 60)); ?>...
                                </p>
                            <?php endif; ?>
                            <div class="product-stock <?php echo $stockClass; ?>">
                                <i class="fas fa-box"></i> <?php echo $stockText; ?> (<?php echo $stock; ?>)
                            </div>
                            <div class="product-price">
                                <?php echo number_format($produit['prix'], 0, ',', ' '); ?> <span>FCA</span>
                            </div>
                            <?php if ($stock > 0): ?>
                            <button onclick="addToCart(<?php echo (int)$produit['id_produit']; ?>)" 
                                    class="btn btn-primary btn-sm" 
                                    style="width: 100%; margin-top: 0.5rem; justify-content: center; display: flex; align-items: center; gap: 8px; cursor: pointer;">
                                <i class="fas fa-cart-plus"></i> Ajouter au panier
                            </button>
                            <?php else: ?>
                            <button disabled 
                                    class="btn btn-secondary btn-sm" 
                                    style="width: 100%; margin-top: 0.5rem; justify-content: center; display: flex; align-items: center; gap: 8px; opacity: 0.6; cursor: not-allowed;">
                                <i class="fas fa-times-circle"></i> Indisponible
                            </button>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <script>
            function addToCart(productId) {
                fetch('update_cart.php?action=ajouter&produit_id=' + productId)
                    .then(function(response) { return response.json(); })
                    .then(function(data) {
                        if (data.success) {
                            // Update cart badge if exists
                            var cartBadge = document.getElementById('cart-badge');
                            if (data.cart_count > 0) {
                                if (!cartBadge) {
                                    var cartLink = document.querySelector('.nav-icon[href="panier.php"]');
                                    if (cartLink) {
                                        var newBadge = document.createElement('span');
                                        newBadge.id = 'cart-badge';
                                        newBadge.className = 'notification-badge';
                                        newBadge.style.background = 'var(--color-terracotta)';
                                        newBadge.textContent = data.cart_count > 9 ? '9+' : data.cart_count;
                                        cartLink.appendChild(newBadge);
                                    }
                                } else {
                                    cartBadge.textContent = data.cart_count > 9 ? '9+' : data.cart_count;
                                }
                            }
                            alert('Produit ajouté au panier !');
                        } else if (data.require_login) {
                            // Rediriger vers la page de connexion
                            if (confirm(data.message + '\n\nVoulez-vous vous connecter?')) {
                                window.location.href = 'login.php';
                            }
                        } else {
                            alert(data.message || 'Erreur lors de l\'ajout au panier');
                        }
                    })
                    .catch(function() {
                        alert('Erreur de connexion');
                    });
            }
            </script>
            
            <div style="text-align: center; margin-top: 2rem;">
                <a href="produit.php" class="btn btn-outline">Voir tous les produits</a>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Points de vente -->
<?php if (!empty($points_vente)): ?>
<section style="background: var(--color-light-gray); padding: 3rem 0; margin-top: 2rem;">
    <div class="container">
        <h2 class="section-title">
            <i class="fas fa-map-marker-alt"></i> Nos Points de Retrait
        </h2>
        <div class="stats-grid">
            <?php foreach ($points_vente as $pdv): ?>
                <div class="stat-card">
                    <div class="stat-icon secondary">
                        <i class="fas fa-store"></i>
                    </div>
                    <div>
                        <div class="stat-value" style="font-size: 1rem;"><?php echo htmlspecialchars($pdv['nom_pdv']); ?></div>
                        <div class="stat-label"><?php echo htmlspecialchars($pdv['adresse_pdv']); ?></div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

</main>
<?php require_once 'includes/footer.php'; ?>

