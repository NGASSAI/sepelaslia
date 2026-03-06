<?php
/**
 * catalogues.php - Page des catalogues et galerie Sepelas&Lia
 * UPLOADEZ CE FICHIER SUR INFINITYFREE
 */

$page_title = 'Catalogues & Galerie - Sepelas&Lia';

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
} catch (Exception $e) {}

// Récupérer les images actives pour la galerie
$images_galerie = [];
try {
    $stmt = $pdo->query("SELECT * FROM images_accueil WHERE actif = 1 ORDER BY position ASC, date_ajout DESC");
    $images_galerie = $stmt->fetchAll();
} catch (Exception $e) {
    $images_galerie = [];
}

// Récupérer les catalogues PDF actifs
$catalogues = [];
try {
    $stmt = $pdo->query("SELECT * FROM catalogues WHERE actif = 1 AND type_fichier = 'pdf' ORDER BY date_ajout DESC");
    $catalogues = $stmt->fetchAll();
} catch (Exception $e) {
    $catalogues = [];
}

require_once 'includes/header.php';
?>

<!-- Lightbox CSS -->
<style>
.lightbox { display: none; position: fixed; z-index: 9999; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.95); justify-content: center; align-items: center; flex-direction: column; }
.lightbox.active { display: flex; }
.lightbox img { max-width: 90%; max-height: 80vh; object-fit: contain; border-radius: 8px; box-shadow: 0 4px 20px rgba(0,0,0,0.5); }
.lightbox-close { position: absolute; top: 20px; right: 30px; color: white; font-size: 40px; cursor: pointer; z-index: 10000; }
.lightbox-nav { position: absolute; top: 50%; transform: translateY(-50%); color: white; font-size: 40px; cursor: pointer; padding: 20px; background: rgba(0,0,0,0.5); border-radius: 50%; }
.lightbox-nav:hover { background: rgba(255,255,255,0.2); }
.lightbox-prev { left: 20px; }
.lightbox-next { right: 20px; }
.lightbox-caption { color: white; margin-top: 15px; font-size: 1.1rem; text-align: center; }
</style>

<!-- Lightbox HTML -->
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
        <!-- Galerie d'images -->
        <?php if (!empty($images_galerie)): ?>
        <h1 style="margin-bottom: 0.5rem; font-size: 1.75rem;">
            <i class="fas fa-images"></i> Notre Galerie
        </h1>
        <p style="color: var(--color-gray); margin-bottom: 2rem;">Cliquez sur une image pour l'agrandir</p>
        
        <div class="galerie-grid">
            <?php foreach ($images_galerie as $index => $img): ?>
                <?php $imagePath = 'uploads/accueil/' . $img['image']; ?>
                <?php if (file_exists(__DIR__ . '/' . $imagePath)): ?>
                <div class="galerie-item" onclick="openLightbox(<?php echo $index; ?>)">
                    <img src="<?php echo htmlspecialchars($imagePath); ?>" alt="<?php echo htmlspecialchars($img['titre'] ?? 'Image'); ?>">
                    <?php if (!empty($img['titre'])): ?>
                        <div class="galerie-overlay"><span><?php echo htmlspecialchars($img['titre']); ?></span></div>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
        
        <style>
        .galerie-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 1rem; margin-bottom: 3rem; }
        .galerie-item { position: relative; aspect-ratio: 16/9; border-radius: 12px; overflow: hidden; cursor: pointer; box-shadow: 0 2px 8px rgba(0,0,0,0.1); transition: transform 0.3s; }
        .galerie-item:hover { transform: translateY(-4px); box-shadow: 0 8px 24px rgba(0,0,0,0.2); }
        .galerie-item img { width: 100%; height: 100%; object-fit: cover; }
        .galerie-overlay { position: absolute; bottom: 0; left: 0; right: 0; background: linear-gradient(to top, rgba(0,0,0,0.8), transparent); padding: 2rem 1rem 1rem; opacity: 0; transition: opacity 0.3s; }
        .galerie-item:hover .galerie-overlay { opacity: 1; }
        .galerie-overlay span { color: white; font-weight: 500; }
        @media (max-width: 768px) { .galerie-grid { grid-template-columns: repeat(2, 1fr); gap: 0.5rem; } }
        @media (max-width: 480px) { .galerie-grid { grid-template-columns: 1fr; } }
        </style>
        <?php endif; ?>
        
        <!-- Catalogues PDF -->
        <?php if (!empty($catalogues)): ?>
        <h1 style="margin-bottom: 0.5rem; font-size: 1.75rem; margin-top: <?php echo !empty($images_galerie) ? '2rem' : '0'; ?>">
            <i class="fas fa-book-open"></i> Nos Catalogues
        </h1>
        
        <div class="catalogues-grid">
            <?php foreach ($catalogues as $cat): ?>
                <?php $filePath = 'uploads/catalogues/' . $cat['fichier']; ?>
                <?php $fileExists = file_exists(__DIR__ . '/' . $filePath); ?>
                <div class="catalogue-card">
                    <div class="catalogue-preview" style="background: #fee2e2;">
                        <i class="fas fa-file-pdf" style="font-size: 4rem; color: #dc2626;"></i>
                    </div>
                    <div class="catalogue-info">
                        <h3><?php echo htmlspecialchars($cat['titre']); ?></h3>
                        <?php if (!empty($cat['description'])): ?>
                            <p><?php echo htmlspecialchars($cat['description']); ?></p>
                        <?php endif; ?>
                        <?php if ($fileExists): ?>
                            <a href="<?php echo htmlspecialchars($filePath); ?>" target="_blank" class="btn btn-primary">
                                <i class="fas fa-eye"></i> Voir / Télécharger
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
        
        <?php if (!empty($images_galerie) || !empty($catalogues)): ?>
        <div style="text-align: center; margin-top: 2rem;">
            <a href="index.php" class="btn btn-outline"><i class="fas fa-home"></i> Retour à l'accueil</a>
        </div>
        <?php endif; ?>
    </div>
</main>

<style>
.catalogues-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 1.5rem; margin-top: 1.5rem; }
.catalogue-card { background: white; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); overflow: hidden; transition: transform 0.2s; }
.catalogue-card:hover { transform: translateY(-4px); box-shadow: 0 4px 16px rgba(0,0,0,0.15); }
.catalogue-preview { height: 180px; display: flex; align-items: center; justify-content: center; }
.catalogue-info { padding: 1.25rem; }
.catalogue-info h3 { margin: 0 0 0.5rem; font-size: 1.1rem; }
.catalogue-info p { margin: 0 0 1rem; font-size: 0.9rem; color: var(--color-gray, #6b7280); }
.btn { display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.75rem 1.25rem; border: none; border-radius: 8px; font-size: 0.9rem; font-weight: 500; cursor: pointer; text-decoration: none; transition: all 0.2s; }
.btn-primary { background: var(--color-primary, #2563eb); color: white; flex: 1; justify-content: center; }
.btn-primary:hover { background: var(--color-primary-dark, #1d4ed8); }
.btn-outline { background: transparent; border: 2px solid var(--color-primary, #2563eb); color: var(--color-primary, #2563eb); }
.btn-outline:hover { background: var(--color-primary, #2563eb); color: white; }
@media (max-width: 640px) { .catalogues-grid { grid-template-columns: 1fr; } }
</style>

<?php require_once 'includes/footer.php'; ?>
