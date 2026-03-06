-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : mar. 24 fév. 2026 à 19:37
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `sepelaslia`
--

-- --------------------------------------------------------

--
-- Structure de la table `categories`
--

CREATE TABLE `categories` (
  `id_categorie` int(11) NOT NULL,
  `nom_categorie` varchar(100) NOT NULL,
  `description_cat` text DEFAULT NULL,
  `image_cat` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `categories`
--

INSERT INTO `categories` (`id_categorie`, `nom_categorie`, `description_cat`, `image_cat`) VALUES
(1, 'Fruits & Légumes', 'Produits naturels frais', NULL),
(2, 'Produits Laitiers', 'Lait, fromage, yaourt', NULL),
(3, 'Céréales & Graines', 'Riz, maïs, legumineuses', NULL),
(4, 'Huiles & Condiments', 'Huiles de cuisine, épices', NULL),
(5, 'Produits Transformés', 'Conserves, snacks, boissons', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `commandes`
--

CREATE TABLE `commandes` (
  `id_commande` int(11) NOT NULL,
  `id_user` int(11) DEFAULT NULL,
  `point_de_vente_id` int(11) DEFAULT NULL,
  `date_commande` timestamp NOT NULL DEFAULT current_timestamp(),
  `date_creation` datetime DEFAULT NULL,
  `total_montant` decimal(10,2) NOT NULL,
  `statut` enum('en_attente','paye','livre','annule') DEFAULT 'en_attente',
  `methode_paiement` varchar(50) DEFAULT 'Cash à la livraison',
  `nom_client` varchar(100) DEFAULT NULL,
  `telephone_client` varchar(20) DEFAULT NULL,
  `email_client` varchar(255) DEFAULT NULL,
  `numero_commande` varchar(50) DEFAULT NULL,
  `id_pdv` int(11) DEFAULT NULL,
  `adresse_livraison` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `commandes`
--

INSERT INTO `commandes` (`id_commande`, `id_user`, `point_de_vente_id`, `date_commande`, `date_creation`, `total_montant`, `statut`, `methode_paiement`, `nom_client`, `telephone_client`, `email_client`, `numero_commande`, `id_pdv`, `adresse_livraison`) VALUES
(12, 7, 5, '2026-02-24 10:37:13', '2026-02-24 11:37:13', 1800.00, '', 'paiement_livraison', 'JUNIOR', '+242055551222', 'JUNIOR@gmail.com', 'CMD-20260224-6267', NULL, NULL),
(13, 7, 5, '2026-02-24 10:51:28', '2026-02-24 11:51:28', 5400.00, '', 'paiement_livraison', 'Ngassai Nathan', '+242066661214', 'nathanzouma@gmail.com', 'CMD-20260224-8915', NULL, NULL),
(14, 7, 5, '2026-02-24 12:16:12', '2026-02-24 13:16:12', 1200.00, '', 'mobile_money', 'Ngassai Nathan', '+242066661214', 'nathanzouma@gmail.com', 'CMD-20260224-7733', NULL, NULL),
(15, 8, 5, '2026-02-24 12:33:34', '2026-02-24 13:33:34', 700.00, '', 'paiement_livraison', 'Ngassai Nathan', '+242066661214', 'nathanzouma@gmail.com', 'CMD-20260224-6798', NULL, NULL),
(16, 7, 2, '2026-02-24 12:36:57', '2026-02-24 13:36:57', 3500.00, '', 'mobile_money', 'Ngassai Nathan', '+242066661214', 'nathanzouma@gmail.com', 'CMD-20260224-6645', NULL, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `details_commande`
--

CREATE TABLE `details_commande` (
  `id_detail` int(11) NOT NULL,
  `id_commande` int(11) DEFAULT NULL,
  `id_produit` int(11) DEFAULT NULL,
  `quantite` int(11) NOT NULL,
  `prix_unitaire` decimal(10,2) NOT NULL,
  `sous_total` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `messages`
--

CREATE TABLE `messages` (
  `id_message` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `nom` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `telephone` varchar(50) DEFAULT NULL,
  `objet` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `reponse` text DEFAULT NULL,
  `date_reponse` datetime DEFAULT NULL,
  `statut` varchar(20) DEFAULT 'nouveau',
  `date_creation` datetime DEFAULT current_timestamp(),
  `created_at` datetime DEFAULT current_timestamp(),
  `lu` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `messages`
--

INSERT INTO `messages` (`id_message`, `user_id`, `nom`, `email`, `telephone`, `objet`, `message`, `reponse`, `date_reponse`, `statut`, `date_creation`, `created_at`, `lu`) VALUES
(1, NULL, 'Ngassai Nathan', 'nathanzouma@gmail.com', '+242066661214', 'teste', 'lllllllllllllllllllll', 'bonjour', NULL, 'nouveau', '2026-02-24 13:35:34', '2026-02-24 13:35:34', 1);

-- --------------------------------------------------------

--
-- Structure de la table `notifications`
--

CREATE TABLE `notifications` (
  `id_notification` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `type` varchar(50) NOT NULL,
  `titre` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `lu` tinyint(1) DEFAULT 0,
  `lien` varchar(255) DEFAULT NULL,
  `date_creation` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `points_de_vente`
--

CREATE TABLE `points_de_vente` (
  `id_pdv` int(11) NOT NULL,
  `nom_pdv` varchar(100) NOT NULL,
  `adresse_pdv` text NOT NULL,
  `ville` varchar(50) DEFAULT 'Brazzaville',
  `telephone_pdv` varchar(20) DEFAULT NULL,
  `actif` tinyint(1) DEFAULT 1,
  `horaires` varchar(100) DEFAULT NULL,
  `telephone` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `points_de_vente`
--

INSERT INTO `points_de_vente` (`id_pdv`, `nom_pdv`, `adresse_pdv`, `ville`, `telephone_pdv`, `actif`, `horaires`, `telephone`) VALUES
(1, 'Chez Maman CLEMENTINE', 'Loutassi, en face de la préfecture', 'Loutassi', '+242 06 681 7726', 1, NULL, NULL),
(2, 'SUPER ALIMENTATION', 'Moungali, avenue de la paix, à côté du Bataclan', 'Moungali', '+242 06 6817726', 1, NULL, NULL),
(3, 'SAJA MARKET', '53, rue MAKOKO, avenue de la paix', 'Centre', '+242 06 681 7726', 1, NULL, NULL),
(4, 'BEIRUT MARKET', 'Centre-ville, avenue de l\'Indépendance', 'Centre-ville', '+242 06 6817726', 1, NULL, NULL),
(5, 'Ngassai Nathan', 'lllllllllllllll', 'Brazzaville', '+242 066817726', 1, '8h-9h', '+242066661214');

-- --------------------------------------------------------

--
-- Structure de la table `produits`
--

CREATE TABLE `produits` (
  `id_produit` int(11) NOT NULL,
  `id_categorie` int(11) DEFAULT NULL,
  `nom` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `prix` decimal(10,2) NOT NULL,
  `unite_mesure` varchar(20) DEFAULT 'kg',
  `stock` int(11) DEFAULT 0,
  `image` varchar(255) DEFAULT NULL,
  `en_vedette` tinyint(1) DEFAULT 0,
  `date_ajout` timestamp NOT NULL DEFAULT current_timestamp(),
  `nom_produit` varchar(255) DEFAULT NULL,
  `stock_quantite` int(11) DEFAULT 0,
  `image_prod` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `produits`
--

INSERT INTO `produits` (`id_produit`, `id_categorie`, `nom`, `description`, `prix`, `unite_mesure`, `stock`, `image`, `en_vedette`, `date_ajout`, `nom_produit`, `stock_quantite`, `image_prod`) VALUES
(14, NULL, 'tof', 'kkkkkkkk', 24.00, 'pièce', 0, 'produit_1771955525_1215.jpeg', 1, '2026-02-24 17:52:05', NULL, 0, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `utilisateurs`
--

CREATE TABLE `utilisateurs` (
  `id_user` int(11) NOT NULL,
  `nom_complet` varchar(150) NOT NULL,
  `email` varchar(100) NOT NULL,
  `mot_de_passe` varchar(255) NOT NULL,
  `telephone` varchar(20) DEFAULT NULL,
  `adresse_livraison` text DEFAULT NULL,
  `role` enum('client','admin') DEFAULT 'client',
  `nb_commandes` int(11) DEFAULT 0,
  `derniere_commande` datetime DEFAULT NULL,
  `categorie_client` varchar(20) DEFAULT 'simple',
  `date_creation` timestamp NOT NULL DEFAULT current_timestamp(),
  `reset_token` varchar(64) DEFAULT NULL,
  `reset_token_expiry` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `utilisateurs`
--

INSERT INTO `utilisateurs` (`id_user`, `nom_complet`, `email`, `mot_de_passe`, `telephone`, `adresse_livraison`, `role`, `nb_commandes`, `derniere_commande`, `categorie_client`, `date_creation`) VALUES
(7, 'JUNIOR', 'JUNIOR@gmail.com', '$2y$12$E9LZ/ZxUi.C81wZ8m1gQYexKPoLyn7sYyOWKIQJsIiXkJT/ySHAY2', '+242 066817726', '', 'client', 0, NULL, 'simple', '2026-02-24 10:20:46'),
(8, 'Administrateur', 'admin@sepelaslia.com', '$2y$10$eXPlon9/OwkPEwtD9hjGQO99fESE6KRiJ2egXJZp2WH1auvL5P7Om', '', NULL, 'admin', 0, NULL, 'simple', '2026-02-24 10:31:54');

-- --------------------------------------------------------

--
-- Structure de la table `visites`
--

CREATE TABLE `visites` (
  `id_visite` int(11) NOT NULL,
  `date_visite` date DEFAULT NULL,
  `adresse_ip` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `visites`
--

INSERT INTO `visites` (`id_visite`, `date_visite`, `adresse_ip`) VALUES
(289, '2026-02-24', '::1');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id_categorie`);

--
-- Index pour la table `commandes`
--
ALTER TABLE `commandes`
  ADD PRIMARY KEY (`id_commande`),
  ADD KEY `id_user` (`id_user`);

--
-- Index pour la table `details_commande`
--
ALTER TABLE `details_commande`
  ADD PRIMARY KEY (`id_detail`),
  ADD KEY `id_commande` (`id_commande`),
  ADD KEY `id_produit` (`id_produit`);

--
-- Index pour la table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id_message`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_statut` (`statut`),
  ADD KEY `idx_date` (`date_creation`);

--
-- Index pour la table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id_notification`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_lu` (`lu`),
  ADD KEY `idx_date` (`date_creation`);

--
-- Index pour la table `points_de_vente`
--
ALTER TABLE `points_de_vente`
  ADD PRIMARY KEY (`id_pdv`);

--
-- Index pour la table `produits`
--
ALTER TABLE `produits`
  ADD PRIMARY KEY (`id_produit`),
  ADD KEY `id_categorie` (`id_categorie`);

--
-- Index pour la table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_reset_token` (`reset_token`);

--
-- Index pour la table `visites`
--
ALTER TABLE `visites`
  ADD PRIMARY KEY (`id_visite`),
  ADD UNIQUE KEY `unique_visit` (`date_visite`,`adresse_ip`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `categories`
--
ALTER TABLE `categories`
  MODIFY `id_categorie` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pour la table `commandes`
--
ALTER TABLE `commandes`
  MODIFY `id_commande` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT pour la table `details_commande`
--
ALTER TABLE `details_commande`
  MODIFY `id_detail` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT pour la table `messages`
--
ALTER TABLE `messages`
  MODIFY `id_message` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id_notification` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `points_de_vente`
--
ALTER TABLE `points_de_vente`
  MODIFY `id_pdv` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pour la table `produits`
--
ALTER TABLE `produits`
  MODIFY `id_produit` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT pour la table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT pour la table `visites`
--
ALTER TABLE `visites`
  MODIFY `id_visite` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=447;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `commandes`
--
ALTER TABLE `commandes`
  ADD CONSTRAINT `commandes_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `utilisateurs` (`id_user`) ON DELETE CASCADE;

--
-- Contraintes pour la table `details_commande`
--
ALTER TABLE `details_commande`
  ADD CONSTRAINT `details_commande_ibfk_1` FOREIGN KEY (`id_commande`) REFERENCES `commandes` (`id_commande`) ON DELETE CASCADE,
  ADD CONSTRAINT `details_commande_ibfk_2` FOREIGN KEY (`id_produit`) REFERENCES `produits` (`id_produit`) ON DELETE CASCADE;

--
-- Contraintes pour la table `produits`
--
ALTER TABLE `produits`
  ADD CONSTRAINT `produits_ibfk_1` FOREIGN KEY (`id_categorie`) REFERENCES `categories` (`id_categorie`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
