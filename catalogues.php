
<?php
/**
 * catalogues.php - Page des catalogues et galerie Sepelas&Lia
 * MODIFIÉ POUR INFINITYFREE - Plus robuste
 */

$page_title = 'Galerie & Catalogues - Sepelas&Lia';

require_once 'config/db.php';

// Auto-créer les tables
try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS images_accueil (
        id_image INT AUTO_INCREMENT PRIMARY KEY,
        titre VARCHAR(255) DEFAULT NULL,
        description TEXT DEFAULT NULL,
        image VARCHAR(255) NOT NULL,
        lien VARCHAR(255) DEFAULT NULL,
        position INT DEFAULT 0,
        actif TINYINT(1) DEFAULT 1,
        date_ajout TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    
    $pdo->exec("CREATE TABLE IF NOT EXISTS catalogues (
        id_catalogue INT AUTO_INCREMENT PRIMARY KEY,
        titre VARCHAR(255) NOT NULL,
        description TEXT DEFAULT NULL,
        fichier VARCHAR(255) NOT NULL,
        type_fichier ENUM('pdf', 'image') DEFAULT 'pdf',
        actif TINYINT(1) DEFAULT 1,
        date_ajout TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
} catch (Exception $e) {}

// Récupérer les images
$images_galerie = [];
try {
    $stmt = $pdo->query("SELECT * FROM images_accueil WHERE actif = 1 ORDER BY position ASC, date_ajout DESC");
    $images_galerie = $stmt->fetchAll();
} catch (Exception $e) {}

// Récupérer les catalogues
$catalogues = [];
try {
    $stmt = $pdo->query("SELECT * FROM catalogues WHERE actif = 1 ORDER BY date_ajout DESC");
    $catalogues = $stmt->fetchAll();
} catch (Exception $e) {}

require_once 'includes/header.php';
?>

<!-- Lightbox -->
<style>
.lightbox { display: none; position: fixed; z-index: 9999; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.95); justify-content: center; align-items: center; flex-direction: column; }
.lightbox.active { display: flex; }
.lightbox img { max-width: 95%; max-height: 85vh; object-fit: contain; border-radius: 8px; box-shadow: 0 0 30px rgba(0,0,0,0.5); }
.lightbox-close { position: absolute; top: 20px; right: 30px; color: white; font-size: 40px; cursor: pointer; }
.lightbox-nav { position: absolute; top: 50%; transform: translateY(-50%); color: white; font-size: 36px; cursor: pointer; padding: 15px; background: rgba(0,0,0,0.6); border-radius: 50%; transition: 0.3s; }
.lightbox-nav:hover { background: rgba(255,255,255,0.3); }
.lightbox-prev { left: 20px; }
.lightbox-next { right: 20px; }
.lightbox-caption { color: white; margin-top: 15px; font-size: 1.1rem; text-align: center; }
</style>

<div class="lightbox" id="lightbox">
    <span class="lightbox-close" onclick="closeLightbox()">&times;</span>
    <span class="lightbox-nav lightbox-prev" onclick="changeImage(-1)">&#10094;</span>
    <img id="lightbox-img" src="" alt="">
    <span class="lightbox-nav lightbox-next" onclick="changeImage(1)">&#10095;</span>
    <div class="lightbox-caption" id="lightbox-caption"></div>
</div>

<script>
let currentImageIndex = 0;
const images = <?php echo json_encode(array_map(function($img) {
    return ['src' => 'uploads/accueil/' . $img['image'], 'caption' => $img['titre'] ?? ''];
}, $images_galerie)); ?>;

function openLightbox(index) {
    if (images.length === 0) return;
    currentImageIndex = index;
    updateLightbox();
    document.getElementById('lightbox').classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeLightbox() {
    document.getElementById('lightbox').classList.remove('active');
    document.body.style.overflow = 'auto';
}

function changeImage(direction) {
    currentImageIndex = (currentImageIndex + direction + images.length) % images.length;
    updateLightbox();
}

function updateLightbox() {
    if (images.length > 0) {
        document.getElementById('lightbox-img').src = images[currentImageIndex].src;
        document.getElementById('lightbox-caption').textContent = images[currentImageIndex].caption;
    }
}

document.addEventListener('keydown', function(e) { if (e.key === 'Escape') closeLightbox(); });
document.getElementById('lightbox').addEventListener('click', function(e) { if (e.target === this) closeLightbox(); });
</script>

<main>
    <div class="container">
        <!-- Galerie Responsive -->
        <?php if (!empty($images_galerie)): ?>
        <h1 style="margin: 1.5rem 0 0.5rem; font-size: 1.75rem; text-align: center;">
            <i class="fas fa-images"></i> Notre Galerie
        </h1>
        <p style="color: var(--color-gray); text-align: center; margin-bottom: 2rem;">
            Cliquez sur une image pour l'agrandir
        </p>
        
        <div class="galerie-grid">
            <?php foreach ($images_galerie as $index => $img): ?>
            <div class="galerie-item" onclick="openLightbox(<?php echo $index; ?>)">
                <img src="uploads/accueil/<?php echo htmlspecialchars($img['image']); ?>" 
                     alt="<?php echo htmlspecialchars($img['titre'] ?? 'Image'); ?>"
                     loading="lazy">
                <?php if (!empty($img['titre'])): ?>
                <div class="galerie-overlay">
                    <span><?php echo htmlspecialchars($img['titre']); ?></span>
                </div>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
        
        <!-- Catalogues -->
        <?php if (!empty($catalogues)): ?>
        <h1 style="margin: 2rem 0 0.5rem; font-size: 1.75rem; text-align: center; <?php echo !empty($images_galerie) ? 'margin-top:3rem;' : ''; ?>">
            <i class="fas fa-book-open"></i> Nos Catalogues
        </h1>
        <p style="color: var(--color-gray); text-align: center; margin-bottom: 2rem;">
            Consultez ou téléchargez nos catalogues
        </p>
        
        <div class="catalogues-grid">
            <?php foreach ($catalogues as $cat): ?>
            <div class="catalogue-card">
                <div class="catalogue-preview">
                    <i class="fas fa-file-pdf"></i>
                </div>
                <div class="catalogue-info">
                    <h3><?php echo htmlspecialchars($cat['titre']); ?></h3>
                    <?php if (!empty($cat['description'])): ?>
                    <p><?php echo htmlspecialchars($cat['description']); ?></p>
                    <?php endif; ?>
                    <a href="uploads/catalogues/<?php echo htmlspecialchars($cat['fichier']); ?>" 
                       target="_blank" class="btn btn-primary">
                        <i class="fas fa-eye"></i> Voir / Télécharger
                    </a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
        
        <!-- Message si vide -->
        <?php if (empty($images_galerie) && empty($catalogues)): ?>
        <div style="text-align: center; padding: 4rem 1rem;">
            <i class="fas fa-folder-open" style="font-size: 4rem; color: #d1d5db; margin-bottom: 1rem;"></i>
            <h2 style="margin-bottom: 1rem;">Aucune image ni catalogue</h2>
            <p style="color: #6b7280; margin-bottom: 2rem;">
                Revenez bientôt pour découvrir nos produits en images !
            </p>
            <a href="index.php" class="btn btn-primary">
                <i class="fas fa-home"></i> Retour à l'accueil
            </a>
        </div>
        <?php else: ?>
        <div style="text-align: center; margin-top: 2rem; padding-bottom: 2rem;">
            <a href="index.php" class="btn btn-outline">
                <i class="fas fa-home"></i> Retour à l'accueil
            </a>
        </div>
        <?php endif; ?>
    </div>
</main>

<!-- Styles Responsive -->
<style>
.galerie-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 1rem;
}

.galerie-item {
    position: relative;
    aspect-ratio: 16/10;
    border-radius: 12px;
    overflow: hidden;
    cursor: pointer;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    transition: transform 0.3s, box-shadow 0.3s;
}

.galerie-item:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.2);
}

.galerie-item img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.4s;
}

.galerie-item:hover img {
    transform: scale(1.1);
}

.galerie-overlay {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    background: linear-gradient(to top, rgba(0,0,0,0.85), transparent);
    padding: 3rem 1rem 1rem;
    opacity: 0;
    transition: opacity 0.3s;
}

.galerie-item:hover .galerie-overlay {
    opacity: 1;
}

.galerie-overlay span {
    color: white;
    font-weight: 600;
    font-size: 1rem;
}

.catalogues-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 1.5rem;
}

.catalogue-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    overflow: hidden;
    transition: transform 0.3s;
}

.catalogue-card:hover {
    transform: translateY(-5px);
}

.catalogue-preview {
    height: 150px;
    background: linear-gradient(135deg, #fee2e2, #fecaca);
    display: flex;
    align-items: center;
    justify-content: center;
}

.catalogue-preview i {
    font-size: 4rem;
    color: #dc2626;
}

.catalogue-info {
    padding: 1.25rem;
}

.catalogue-info h3 {
    margin: 0 0 0.5rem;
    font-size: 1.1rem;
    color: #1f2937;
}

.catalogue-info p {
    margin: 0 0 1rem;
    font-size: 0.9rem;
    color: #6b7280;
}

.btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    padding: 0.75rem 1.25rem;
    border: none;
    border-radius: 8px;
    font-size: 0.9rem;
    font-weight: 500;
    cursor: pointer;
    text-decoration: none;
    transition: all 0.2s;
    width: 100%;
}

.btn-primary {
    background: #2563eb;
    color: white;
}

.btn-primary:hover {
    background: #1d4ed8;
}

.btn-outline {
    background: transparent;
    border: 2px solid #2563eb;
    color: #2563eb;
    width: auto;
}

.btn-outline:hover {
    background: #2563eb;
    color: white;
}

/* Responsive Mobile */
@media (max-width: 768px) {
    .galerie-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 0.5rem;
    }
    
    .galerie-item {
        aspect-ratio: 1;
        border-radius: 8px;
    }
    
    .catalogues-grid {
        grid-template-columns: 1fr;
    }
    
    .lightbox-nav {
        font-size: 24px;
        padding: 10px;
    }
    
    .lightbox-prev { left: 5px; }
    .lightbox-next { right: 5px; }
}

@media (max-width: 480px) {
    .galerie-grid {
        grid-template-columns: 1fr;
    }
    
    h1 {
        font-size: 1.4rem !important;
    }
}
</style>

<?php require_once 'includes/footer.php'; ?>

