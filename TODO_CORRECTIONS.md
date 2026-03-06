# TODO - Corrections des problèmes signalés

## Problèmes à résoudre:
1. [x] Corriger le fuseau horaire MySQL (dates Congo Brazzaville)
2. [ ] Boutons de liens ne fonctionnent plus sur la page suivi

## Corrections effectuées:

### 1. Fuseau horaire MySQL corrigé
- [x] config/db.php - Ajout SET time_zone = '+01:00'
- [x] config/db_infinityfree.php - Ajout SET time_zone = '+01:00'  
- [x] includes/db.php - Ajout SET time_zone = '+01:00'

### 2. Liens à vérifier
- [ ] Le code des liens semble correct
- [ ] Le problème pourrait être le cache du navigateur après mise à jour


