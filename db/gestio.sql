-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Hôte : mysql
-- Généré le : mer. 03 juin 2026 à 07:20
-- Version du serveur : 8.4.9
-- Version de PHP : 8.3.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `gestio`
--

-- --------------------------------------------------------

--
-- Structure de la table `category`
--

CREATE TABLE `category` (
  `id_category` int NOT NULL,
  `name` varchar(50) NOT NULL,
  `color` varchar(7) NOT NULL,
  `id_user` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `category`
-- (catégories par défaut du compte de démonstration Eva ; chaque nouveau
--  compte reçoit sa propre copie via seedDefaultCategories() à l'inscription)
--

INSERT INTO `category` (`id_category`, `name`, `color`, `id_user`) VALUES
(1, 'Food', '#28a745', 1),
(2, 'Travel', '#007bff', 1),
(3, 'Housing', '#dc3545', 1),
(4, 'Hobbies', '#ffc107', 1),
(5, 'Health', '#17a2b8', 1),
(6, 'Education', '#6610f2', 1),
(7, 'Other', '#6c757d', 1);

-- --------------------------------------------------------

--
-- Structure de la table `expense`
--

CREATE TABLE `expense` (
  `id_expense` int NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `title` varchar(255) NOT NULL,
  `expense_date` date NOT NULL,
  `id_category` int NOT NULL,
  `id_user` int NOT NULL,
  `note` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `expense`
--

INSERT INTO `expense` (`id_expense`, `amount`, `title`, `expense_date`, `id_category`, `id_user`) VALUES
(1, 15.00, 'Cinema', '2026-06-03', 4, 1);

-- --------------------------------------------------------

--
-- Structure de la table `user`
--

CREATE TABLE `user` (
  `id_user` int NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `monthly_budget` decimal(15,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `user`
--

-- Mot de passe hashé avec password_hash() (bcrypt). Identifiants de test :
--   email : eva@evatest.com   |   mot de passe : eva@evatest
INSERT INTO `user` (`id_user`, `username`, `email`, `password`) VALUES
(1, 'Eva', 'eva@evatest.com', '$2y$12$SSBovUcqOCSQISw8cqr8VuZh.uPrr887pPN38RmRSKYWXjqqNq7.y');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`id_category`),
  ADD KEY `id_user` (`id_user`);

--
-- Index pour la table `expense`
--
ALTER TABLE `expense`
  ADD PRIMARY KEY (`id_expense`),
  ADD KEY `id_category` (`id_category`),
  ADD KEY `id_user` (`id_user`);

--
-- Index pour la table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `uq_user_email` (`email`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `category`
--
ALTER TABLE `category`
  MODIFY `id_category` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT pour la table `expense`
--
ALTER TABLE `expense`
  MODIFY `id_expense` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `user`
--
ALTER TABLE `user`
  MODIFY `id_user` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `expense`
--
ALTER TABLE `expense`
  ADD CONSTRAINT `expense_ibfk_1` FOREIGN KEY (`id_category`) REFERENCES `category` (`id_category`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `expense_ibfk_2` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `category`
--
ALTER TABLE `category`
  ADD CONSTRAINT `category_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE;

-- --------------------------------------------------------

--
-- Structure de la table `remember_token` (sessions persistantes "Se souvenir de moi")
--

CREATE TABLE `remember_token` (
  `id_token` int NOT NULL AUTO_INCREMENT,
  `id_user` int NOT NULL,
  `token_hash` char(64) NOT NULL,
  `expires_at` datetime NOT NULL,
  PRIMARY KEY (`id_token`),
  UNIQUE KEY `uq_token_hash` (`token_hash`),
  KEY `id_user` (`id_user`),
  CONSTRAINT `remember_token_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `password_reset` (tokens de réinitialisation de mot de passe, valides 1h)
--

CREATE TABLE `password_reset` (
  `id_reset` int NOT NULL AUTO_INCREMENT,
  `id_user` int NOT NULL,
  `token_hash` char(64) NOT NULL,
  `expires_at` datetime NOT NULL,
  PRIMARY KEY (`id_reset`),
  UNIQUE KEY `uq_reset_token_hash` (`token_hash`),
  KEY `id_user` (`id_user`),
  CONSTRAINT `password_reset_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `login_attempt` (anti-brute-force /login, #24)
-- Trace les échecs de connexion par IP et par email pour appliquer un
-- verrouillage temporaire. Pas de FK : l'email peut viser un compte inexistant.
--

CREATE TABLE `login_attempt` (
  `id_attempt` int NOT NULL AUTO_INCREMENT,
  `ip` varchar(45) NOT NULL,
  `email` varchar(255) NOT NULL,
  `attempted_at` datetime NOT NULL,
  PRIMARY KEY (`id_attempt`),
  KEY `idx_ip_time` (`ip`,`attempted_at`),
  KEY `idx_email_time` (`email`,`attempted_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `recurring_expense` (dépenses mensuelles automatiques)
--

CREATE TABLE `recurring_expense` (
  `id_recurring` int NOT NULL AUTO_INCREMENT,
  `id_user` int NOT NULL,
  `title` varchar(255) NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `id_category` int NOT NULL,
  `day_of_month` tinyint NOT NULL DEFAULT '1',
  `active` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id_recurring`),
  KEY `id_user` (`id_user`),
  KEY `id_category` (`id_category`),
  CONSTRAINT `recurring_expense_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `recurring_expense_ibfk_2` FOREIGN KEY (`id_category`) REFERENCES `category` (`id_category`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `recurring_expense_log` (trace des mois déjà générés)
--

CREATE TABLE `recurring_expense_log` (
  `id_log` int NOT NULL AUTO_INCREMENT,
  `id_recurring` int NOT NULL,
  `year_month` char(7) NOT NULL,
  PRIMARY KEY (`id_log`),
  UNIQUE KEY `uq_log_recurring_month` (`id_recurring`,`year_month`),
  CONSTRAINT `recurring_expense_log_ibfk_1` FOREIGN KEY (`id_recurring`) REFERENCES `recurring_expense` (`id_recurring`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `budget` (plafond mensuel récurrent par catégorie et par utilisateur)
--

CREATE TABLE `budget` (
  `id_budget` int NOT NULL,
  `id_user` int NOT NULL,
  `id_category` int NOT NULL,
  `amount` decimal(15,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Index pour la table `budget`
--
ALTER TABLE `budget`
  ADD PRIMARY KEY (`id_budget`),
  ADD UNIQUE KEY `uq_budget_user_cat` (`id_user`,`id_category`),
  ADD KEY `id_category` (`id_category`);

--
-- AUTO_INCREMENT pour la table `budget`
--
ALTER TABLE `budget`
  MODIFY `id_budget` int NOT NULL AUTO_INCREMENT;

--
-- Contraintes pour la table `budget`
--
ALTER TABLE `budget`
  ADD CONSTRAINT `budget_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `budget_ibfk_2` FOREIGN KEY (`id_category`) REFERENCES `category` (`id_category`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
