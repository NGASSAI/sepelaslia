<?php
/**
 * produit.php - Liste des produits avec images professionnelles
 */

$page_title = 'Produits - Sepelas&Lia';

require_once 'config/db.php';

// Récupérer les produits
 [];
try {
   $produits = // Afficher tous les produits avec stock > 0
    $stmt = $pdo->query("SELECT * FROM produits WHERE stock > 0 ORDER BY date_ajout DESC");
    $produits = $stmt->fetchAll();
} catch (PDOException $e) {
    $produits = [];
}

// Fonction pour vérifier si l'image existe
function imageExists($path) {
    return file_exists(__DIR__ . '/' . $path);
}

require_once 'includes/header.php';
?>

<main>
    <div class="container">
        <h1 style="margin-bottom: 1.5rem; font-size: 1.5rem;">Nos Produits</h1>
        
        <?php if (empty($produits)): ?>
            <div class="card">
                <div class="card-body text-center">
                    <p class="text-muted">Aucun produit disponible pour le moment.</p>
                </div>
            </div>
        <?php else: ?>
            <div class="products-grid">
                <?php foreach ($produits as $produit): ?>
                    <div class="product-card">
                        <?php
                        // Gestion professionnelle des images
                        $rawImage = (string)($produit['image'] ?? '');
                        $imageName = $rawImage ? basename(str_replace('\\', '/', $rawImage)) : '';
                        $imageSrc = 'uploads/' . $imageName;
                        $hasImage = !empty($imageName) && imageExists($imageSrc);
                        ?>
                        
                        <?php if ($hasImage): ?>
                            <img
                                src="<?php echo htmlspecialchars($imageSrc); ?>"
                                data-lightbox="<?php echo htmlspecialchars($imageSrc); ?>"
                                alt="<?php echo htmlspecialchars($produit['nom']); ?>"
                                class="product-image"
                                style="object-fit: cover; border-radius: 8px;"
                                loading="lazy">
                        <?php else: ?>
                            <div class="product-image" style="background: #f5f5f5; border-radius: 8px; display: flex; align-items: center; justify-content: center; height: 200px; aspect-ratio: 3/2;">
                                <i class="fas fa-image" style="font-size: 4rem; color: #cccccc;"></i>
                            </div>
                        <?php endif; ?>
                        
                        <div class="product-info">
                            <h3 class="product-name"><?php echo htmlspecialchars($produit['nom']); ?></h3>
                            <p style="font-size: 0.85rem; color: var(--color-gray); margin-bottom: 0.5rem;">
                                <?php echo htmlspecialchars($produit['description'] ?? ''); ?>
                            </p>
                            <p class="product-price"><?php echo number_format($produit['prix'], 0, ',', ' '); ?> FCA</p>
                            <?php if (isset($produit['stock'])): ?>
                                <p style="font-size: 0.75rem; color: var(--color-gray);">
                                    <i class="fas fa-box"></i> Stock: <?php echo $produit['stock']; ?>
                                </p>
                            <?php endif; ?>
                            
                            <button type="button" onclick="ajouterAuPanier(<?php echo (int)$produit['id_produit']; ?>, <?php echo (int)$produit['prix']; ?>, <?php echo (int)$produit['stock']; ?>)" 
                                    class="btn btn-primary btn-sm" style="width: 100%; margin-top: 0.75rem;">
                                <i class="fas fa-cart-plus"></i> Ajouter au panier
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</main>

<script>
// Fonction ajouter au panier via AJAX - avec vérification de connexion
function ajouterAuPanier(produitId, prix, stock) {
    fetch('update_cart.php?action=ajouter&produit_id=' + produitId)
        .then(function(response) { return response.json(); })
        .then(function(data) {
            if (data.success) {
                // Mettre à jour le compteur du panier dans le header
                var cartBadge = document.getElementById('cart-count');
                if (cartBadge) {
                    cartBadge.textContent = data.cart_count;
                } else {
                    var cartLink = document.querySelector('a[href="panier.php"]');
                    if (cartLink) {
                        var newBadge = document.createElement('span');
                        newBadge.id = 'cart-count';
                        newBadge.className = 'notification-badge';
                        newBadge.style.cssText = 'position:absolute;top:-2px;right:-2px;background:#ef4444;color:white;font-size:0.65rem;font-weight:700;min-width:18px;height:18px;border-radius:50%;display:flex;align-items:center;justify-content:center;';
                        newBadge.textContent = data.cart_count;
                        cartLink.style.position = 'relative';
                        cartLink.appendChild(newBadge);
                    }
                }
                alert('Produit ajouté au panier! (' + data.cart_count + ' article(s))');
            } else if (data.require_login) {
                // Rediriger vers la page de connexion
                if (confirm(data.message + '\n\nVoulez-vous vous connecter?')) {
                    window.location.href = 'login.php';
                }
            } else {
                alert(data.message || 'Erreur lors de l\'ajout au panier');
            }
        })
        .catch(function(error) {
            alert('Erreur de connexion');
        });
}
</script>

<?php require_once 'includes/footer.php'; ?>

