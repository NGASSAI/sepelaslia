<?php
/**
 * index.php - Page d'accueil Sepelas&Lia
 * UPLOADEZ CE FICHIER SUR INFINITYFREE
 */

$page_title = 'Sepelas&Lia - Produits Naturels';

// Connexion à la base
require_once 'config/db.php';

// Auto-créer les tables si elles n'existent pas
try {
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS images_accueil (
            id_image INT AUTO_INCREMENT PRIMARY KEY,
            titre VARCHAR(255) DEFAULT NULL,
            description TEXT DEFAULT NULL,
            image VARCHAR(255) NOT NULL,
            lien VARCHAR(255) DEFAULT NULL,
            position INT DEFAULT 0,
            actif TINYINT(1) DEFAULT 1,
            date_ajout TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
    ");
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS catalogues (
            id_catalogue INT AUTO_INCREMENT PRIMARY KEY,
            titre VARCHAR(255) NOT NULL,
            description TEXT DEFAULT NULL,
            fichier VARCHAR(255) NOT NULL,
            type_fichier ENUM('pdf', 'image') DEFAULT 'pdf',
            actif TINYINT(1) DEFAULT 1,
            date_ajout TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
    ");
} catch (Exception $e) {
    // Ignorer si les tables existent déjà
}

// Charger les produits
$produits = [];
try {
    $stmt = $pdo->query("SELECT * FROM produits WHERE stock > 0 ORDER BY date_ajout DESC LIMIT 8");
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
                        ?>
                        <img src="<?php echo htmlspecialchars($imageSrc); ?>"
                             data-lightbox="<?php echo htmlspecialchars($imageSrc); ?>"
                             alt="<?php echo htmlspecialchars($produit['nom']); ?>"
                             class="product-image"
                             onerror="this.src='https://via.placeholder.com/300x200?text=Produit'">
                        <div class="product-info">
                            <h3 class="product-name"><?php echo htmlspecialchars($produit['nom']); ?></h3>
                            <?php if (!empty($produit['description'])): ?>
                                <p style="font-size: 0.85rem; color: var(--color-gray); margin-bottom: 0.5rem; line-height: 1.4;">
                                    <?php echo htmlspecialchars(substr($produit['description'], 0, 60)); ?>...
                                </p>
                            <?php endif; ?>
                            <div class="product-price">
                                <?php echo number_format($produit['prix'], 0, ',', ' '); ?> <span>FCA</span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
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
