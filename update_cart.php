<?php
/**
 * update_cart.php - Gestion des quantites du panier en AJAX
 * Retourne JSON pour les requetes AJAX
 * L'utilisateur doit etre connecte pour ajouter au panier
 */

session_start();

header('Content-Type: application/json');

// Verifier que l'utilisateur est connecte pour ajouter au panier
if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Veuillez vous connecter pour ajouter au panier',
        'cart_count' => 0,
        'item_subtotal' => 0,
        'cart_total' => 0,
        'require_login' => true
    ]);
    exit;
}

// Initialiser le panier si necessaire
if (!isset($_SESSION['panier'])) {
    $_SESSION['panier'] = [];
}

$response = [
    'success' => false,
    'message' => '',
    'cart_count' => 0,
    'item_subtotal' => 0,
    'cart_total' => 0
];

// Verifier la methode de requete
if ($_SERVER['REQUEST_METHOD'] === 'POST' || $_SERVER['REQUEST_METHOD'] === 'GET') {
    $action = $_POST['action'] ?? $_GET['action'] ?? '';
    $produit_id = $_POST['produit_id'] ?? $_GET['produit_id'] ?? 0;
    $quantite = $_POST['quantite'] ?? $_GET['quantite'] ?? 1;
    
    // Convertir en entier
    $produit_id = (int)$produit_id;
    $quantite = (int)$quantite;
    
    if ($produit_id <= 0) {
        $response['message'] = 'ID produit invalide';
        echo json_encode($response);
        exit;
    }
    
    // Handle add/ajouter action BEFORE the switch
    if (in_array($action, ['add', 'ajouter'])) {
        try {
            require_once 'config/db.php';
            $stmt = $pdo->prepare("SELECT prix, stock FROM produits WHERE id_produit = ?");
            $stmt->execute([$produit_id]);
            $produit = $stmt->fetch();
            
            if ($produit && $produit['stock'] > 0) {
                if (!isset($_SESSION['panier'][$produit_id])) {
                    $_SESSION['panier'][$produit_id] = [
                        'quantite' => 1,
                        'prix' => (int)$produit['prix']
                    ];
                } else {
                    $_SESSION['panier'][$produit_id]['quantite']++;
                }
                $response['success'] = true;
                $response['message'] = 'Produit ajouté au panier';
            } else {
                $response['message'] = 'Produit non disponible en stock';
            }
        } catch (Exception $e) {
            $response['message'] = 'Erreur: ' . $e->getMessage();
        }
        
        // Calculate total and item count
        $total = 0;
        $nombre_articles = 0;
        
        foreach ($_SESSION['panier'] as $produit) {
            $total += $produit['prix'] * $produit['quantite'];
            $nombre_articles += $produit['quantite'];
        }
        
        $response['cart_count'] = $nombre_articles;
        $response['cart_total'] = $total;
        
        echo json_encode($response);
        exit;
    }
    
    switch ($action) {
        case 'update':
        case 'modifier':
            // Mettre a jour la quantite
            if (isset($_SESSION['panier'][$produit_id])) {
                if ($quantite <= 0) {
                    // Supprimer l'article si quantite <= 0
                    unset($_SESSION['panier'][$produit_id]);
                    $response['message'] = 'Article supprime';
                } else {
                    $_SESSION['panier'][$produit_id]['quantite'] = $quantite;
                    $response['item_subtotal'] = $_SESSION['panier'][$produit_id]['prix'] * $quantite;
                    $response['message'] = 'Quantite mise a jour';
                }
                $response['success'] = true;
            } else {
                $response['message'] = 'Article non trouve dans le panier';
            }
            break;
            
        case 'remove':
        case 'supprimer':
            // Supprimer l'article
            if (isset($_SESSION['panier'][$produit_id])) {
                unset($_SESSION['panier'][$produit_id]);
                $response['success'] = true;
                $response['message'] = 'Article supprime';
            } else {
                $response['message'] = 'Article non trouve';
            }
            break;
            
        case 'increase':
        case '+':
            // Augmenter la quantite
            if (isset($_SESSION['panier'][$produit_id])) {
                $_SESSION['panier'][$produit_id]['quantite']++;
                $response['item_subtotal'] = $_SESSION['panier'][$produit_id]['prix'] * $_SESSION['panier'][$produit_id]['quantite'];
                $response['success'] = true;
                $response['message'] = 'Quantite augmentee';
            } else {
                $response['message'] = 'Article non trouve';
            }
            break;
            
        case 'decrease':
        case '-':
            // Diminuer la quantite
            if (isset($_SESSION['panier'][$produit_id])) {
                $_SESSION['panier'][$produit_id]['quantite']--;
                if ($_SESSION['panier'][$produit_id]['quantite'] <= 0) {
                    unset($_SESSION['panier'][$produit_id]);
                    $response['message'] = 'Article supprime (quantite zero)';
                } else {
                    $response['item_subtotal'] = $_SESSION['panier'][$produit_id]['prix'] * $_SESSION['panier'][$produit_id]['quantite'];
                    $response['success'] = true;
                    $response['message'] = 'Quantite diminuee';
                }
            } else {
                $response['message'] = 'Article non trouve';
            }
            break;
            
        case 'clear':
        case 'vider':
            // Vider le panier
            $_SESSION['panier'] = [];
            $response['success'] = true;
            $response['message'] = 'Panier vide';
            break;
            
        default:
            $response['message'] = 'Action inconnue: ' . $action;
    }
    
    // Calculer le total et le nombre d'articles
    $total = 0;
    $nombre_articles = 0;
    
    foreach ($_SESSION['panier'] as $produit) {
        $total += $produit['prix'] * $produit['quantite'];
        $nombre_articles += $produit['quantite'];
    }
    
    $response['cart_count'] = $nombre_articles;
    $response['cart_total'] = $total;
    
} else {
    $response['message'] = 'Methode non autorisee';
}

echo json_encode($response);
exit;

