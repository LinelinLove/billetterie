SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


-- Base de données : `billetterie_hetic`
CREATE DATABASE `billetterie_hetic`;
USE `billetterie_hetic`;

-- Structure de la table `events`
USE `billetterie_hetic`;
CREATE TABLE `events` (
  `id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `type` varchar(20) DEFAULT NULL,
  `date_event` date DEFAULT NULL,
  `date_create` date DEFAULT NULL,
  `qrcode` varchar(255) DEFAULT NULL,
  `creator` varchar(30) DEFAULT NULL,
  `status` varchar(30) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- Structure de la table `events_users`
CREATE TABLE `events_users` (
  `event_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- Insert
INSERT INTO `events_users` (`event_id`, `user_id`) VALUES
(31, 7);

-- Alter table
ALTER TABLE `events_users`
  ADD UNIQUE KEY `unique_event_user` (`event_id`,`user_id`);
COMMIT;

-- Déchargement des données de la table `events`

-- Déchargement des données de la table `events`
INSERT INTO `events` (`id`, `name`, `type`,`date_event`, `date_create`, `qrcode`, `creator`, `status`) VALUES 
(1, 'Ciné-Klap', 'Avant-première','2023-04-20', '2023-04-27', 'test', 'Valentin Machefaux', 'Passé'),
(2, 'Genshin Impact', 'Concert', '2023-05-19', '2023-04-25', 'test', 'Hoyo-Mix', 'À venir');


-- Structure de la table `tickets`
USE `billetterie_hetic`;
CREATE TABLE `tickets` (
  `id` int(11) NOT NULL,
  `event_id` int(11) DEFAULT NULL,
  `code_public` varchar(30) DEFAULT NULL,
  `code_private` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- Structure de la table `users`
USE `billetterie_hetic`;
CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `first_name` varchar(255) DEFAULT NULL,
  `last_name` varchar(255) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `phone_number` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- Table `users`
INSERT INTO `users` (`id`, `first_name`, `last_name`, `email`, `phone_number`) VALUES
(7, 'Tharishanan', 'Mahalinham', 'k.tharishanan@gmail.com', '0123456789');

-- Structure de la table `tokens`
USE `billetterie_hetic`;
CREATE TABLE `tokens` (
  `id` int(11) UNSIGNED NOT NULL,
  `token` varchar(64) NOT NULL,
  `expiration` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8
COLLATE=utf8_general_ci;

-- Structure de la table `admins`
USE `billetterie_hetic`;
CREATE table `admins` (
    `id` int(11) NOT NULL,
  `login` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `first_name` varchar(255) DEFAULT NULL,
  `last_name` varchar(255) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `phone_number` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


-- Déchargement des données de la table `tokens`
INSERT INTO `tokens` (`id`, `token`, `expiration`) VALUES
(1, '8b6b7f635d027b3f85d564f9c1a18ada7b262d1bd67eb3c57c76ad57eee04729', '2023-04-26 11:56:55'),
(2, '8f753872d82f596fb0bb979997f8d1b31959f8641e280434e09d9e550fd973ff', '2023-04-26 11:22:16');

-- Index pour la table `events`
USE `billetterie_hetic`;
ALTER TABLE `events`
  ADD PRIMARY KEY (`id`);

-- Index pour la table `admins`
USE `billetterie_hetic`;
INSERT INTO `admins` (`id`, `login`, `password`, `first_name`, `last_name`, `email`, `phone_number`) VALUES
(1, 'admin', '$2y$10$eLVtcT2ssJW4dhzF6S2r2eJc1ICK5Pxks2js9u41FG5Twv2huUZqa', 'Valentine', 'Mahalinham', 'Valentine.M@gmail.com', '0123456789');

-- Index pour la table `tickets`
USE `billetterie_hetic`;
ALTER TABLE `tickets`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code_public` (`code_public`),
  ADD UNIQUE KEY `code_private` (`code_private`),
  ADD KEY `event_id` (`event_id`);

-- Index pour la table `admins`
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_email` (`email`),
  ADD UNIQUE KEY `unique_login` (`login`),
  ADD UNIQUE KEY `phone_number` (`phone_number`);

-- Index pour la table `users`
USE `billetterie_hetic`;
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_email` (`email`),
  ADD UNIQUE KEY `phone_number` (`phone_number`);

-- AUTO_INCREMENT pour la table `events`
USE `billetterie_hetic`;
ALTER TABLE `events`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

-- AUTO_INCREMENT pour la table `tickets`
USE `billetterie_hetic`;
ALTER TABLE `tickets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

-- AUTO_INCREMENT pour la table `users`
USE `billetterie_hetic`;
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
COMMIT;

-- Index pour la table `tokens`
USE `billetterie_hetic`;
ALTER TABLE `tokens`
  ADD PRIMARY KEY (`id`);

-- AUTO_INCREMENT pour la table `tokens` 
USE `billetterie_hetic`;
ALTER TABLE `tokens`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=108;
COMMIT;

-- AUTO_INCREMENT pour la table `admins` 
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;