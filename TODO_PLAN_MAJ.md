# PLAN DE MISE À JOUR - SEPELAS&LIA

## TÂCHES IDENTIFIÉES

### 1. MODIFIER PRODUIT (produit.php)
- Ajouter un bouton "Modifier" sur chaque carte produit
- Créer une page admin pour modifier les produits: `admin/modifier-produit.php`

### 2. MES COMMANDES PROFESSIONNEL (mes-commandes.php)
- Refonte complète avec un design professionnel
- Ajout de détails de commande (items, quantités)
- Timeline visuelle du statut
- Actions possibles par client

### 3. CORRECTION admin/commandes.php
- Ligne 107: Warning "completee" - corriger le tableau $stats
- Ligne 103: Warning "confirmee" - corriger le tableau $stats

### 4. LOGIQUE DE NOTIFICATION PROFESSIONNELLE
- Animation de clignotement (blinking) quand nouvelle notification
- Notification persistante si non lue (ne s'enlève pas automatiquement)
- Même logique pour l'admin
- Ajout d'un système de notification professionnel

### 5. INTÉGRATION CHAT
- Ajouter un accès rapide au chat dans le header client
- Ajouter le nombre de messages non lus avec indication visuelle

## FICHIERS À MODIFIER

1. `produit.php` - Ajouter boutons modifier
2. `admin/produits.php` - Ajouter boutons modifier  
3. `admin/ajouter-produit.php` - Sauvegarder pour réutilisation
4. `admin/modifier-produit.php` - NOUVEAU - Formulaire modification
5. `mes-commandes.php` - Refonte professionnelle
6. `admin/commandes.php` - Corriger warnings et améliorer design
7. `includes/header.php` - Notifications pro avec blinking
8. `admin/admin-header.php` - Notifications pro avec blinking
9. `mark_notification_read.php` - Améliorer logique
10. `assets/css/client.css` - Ajouter styles blinking
11. `assets/css/admin.css` - Ajouter styles blinking

## ÉTAPES D'EXÉCUTION

1. Créer admin/modifier-produit.php
2. Modifier produit.php et admin/produits.php pour ajouter liens modifier
3. Corriger admin/commandes.php (warnings)
4. Refaire mes-commandes.php en version pro
5. Améliorer header.php avec notifications blinking
6. Améliorer admin-header.php avec notifications blinking
7. Ajouter CSS pour animations

