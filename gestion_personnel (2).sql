-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : lun. 19 oct. 2026 à 11:32
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `gestion_personnel`
--

-- --------------------------------------------------------

--
-- Structure de la table `disputes`
--

CREATE TABLE `disputes` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) DEFAULT NULL,
  `type` enum('Interne','Externe') NOT NULL DEFAULT 'Interne',
  `categorie` varchar(100) NOT NULL,
  `gravite` enum('Faible','Moyen','Élevé','Critique') NOT NULL DEFAULT 'Moyen',
  `objet` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `tiers_impliques` varchar(255) DEFAULT NULL,
  `date_ouverture` date NOT NULL,
  `date_limite_reponse` date DEFAULT NULL,
  `date_cloture` date DEFAULT NULL,
  `statut` enum('En cours','En attente','En justice','Résolu','Classé sans suite') NOT NULL DEFAULT 'En cours',
  `decision` text DEFAULT NULL,
  `impact_financier` decimal(12,2) DEFAULT 0.00,
  `fichier_joint` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `disputes`
--

INSERT INTO `disputes` (`id`, `employee_id`, `type`, `categorie`, `gravite`, `objet`, `description`, `tiers_impliques`, `date_ouverture`, `date_limite_reponse`, `date_cloture`, `statut`, `decision`, `impact_financier`, `fichier_joint`, `created_at`, `updated_at`) VALUES
(1, 2, 'Interne', 'Discipline', 'Moyen', 'Dispute & Bagarre ', 'Le chauffeur a été impliqué dans une altercation avec un tiers ayant dégénéré en dispute et en bagarre. L’incident a entraîné une intervention des personnes présentes afin de mettre fin à l’altercation. Les circonstances exactes et les éventuelles responsabilités restent à établir.', '', '2026-09-08', '2026-09-30', NULL, 'En justice', 'le contentieux est finalement resolu', 200000.00, 'contentieux_1788971366_6aa18966cca8b.pdf', '2026-09-09 14:04:48', '2026-09-09 16:51:49');

-- --------------------------------------------------------

--
-- Structure de la table `dispute_documents`
--

CREATE TABLE `dispute_documents` (
  `id` int(11) NOT NULL,
  `dispute_id` int(11) NOT NULL,
  `nom_fichier` varchar(255) NOT NULL,
  `titre_document` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `dispute_updates`
--

CREATE TABLE `dispute_updates` (
  `id` int(11) NOT NULL,
  `dispute_id` int(11) NOT NULL,
  `statut` varchar(50) NOT NULL,
  `decision` text NOT NULL,
  `fichier_joint` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `dispute_updates`
--

INSERT INTO `dispute_updates` (`id`, `dispute_id`, `statut`, `decision`, `fichier_joint`, `created_at`) VALUES
(1, 1, 'En attente', 'le dossier a evolué. Nous avons une premiere rencontre avec le chauffeur. voici le PV en PJ', 'update_1788972111_6aa18c4f62859.pdf', '2026-09-09 16:41:51'),
(2, 1, 'En justice', 'Finalement le Dossier a été transférer en justice', 'update_1788972152_6aa18c78ab41e.pdf', '2026-09-09 16:42:32');

-- --------------------------------------------------------

--
-- Structure de la table `employees`
--

CREATE TABLE `employees` (
  `id` int(11) NOT NULL,
  `entreprise` varchar(50) NOT NULL DEFAULT 'STC',
  `matricule` varchar(20) NOT NULL,
  `nom` varchar(50) NOT NULL,
  `prenom` varchar(50) NOT NULL,
  `email` varchar(150) DEFAULT NULL,
  `etat_civil` varchar(50) DEFAULT NULL,
  `adresse` varchar(255) DEFAULT NULL,
  `telephone` varchar(50) DEFAULT NULL,
  `cni` varchar(50) DEFAULT NULL,
  `date_embauche` date DEFAULT NULL,
  `type_contrat` enum('CDD','CDI','Stage','Autre') NOT NULL DEFAULT 'CDI',
  `numero_permis` varchar(50) DEFAULT NULL,
  `categories_permis` varchar(50) DEFAULT NULL,
  `date_expiration_permis` date DEFAULT NULL,
  `intitule_poste` varchar(150) DEFAULT NULL,
  `contrat_pdf` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `employees`
--

INSERT INTO `employees` (`id`, `entreprise`, `matricule`, `nom`, `prenom`, `email`, `etat_civil`, `adresse`, `telephone`, `cni`, `date_embauche`, `type_contrat`, `numero_permis`, `categories_permis`, `date_expiration_permis`, `intitule_poste`, `contrat_pdf`, `created_at`) VALUES
(1, 'STC', 'EMP-2026-01', 'FALL', 'Masse', 'fallmasse001@gmail.com', 'Marié(e)', 'Parcelles Assainies U6', '+221784148224', '1 2154 1981 20022', '2026-09-04', 'CDI', '2150002545', 'B', '2030-01-01', 'Chauffeur', '1788541244_6a9af93c5fe27.pdf', '2026-09-04 17:00:44'),
(2, 'STC', 'EMP-2026-02', 'DIEYE', 'Serigne Saliou', 'saliou@gmail.com', 'Marié(e)', 'Parcelles Assainies U15', '+221771234567', '1 0278 1989 56899', '2026-01-01', 'CDI', '5698702854', 'A', '2029-12-31', 'Chauffeur', '1788601232_6a9be390d38f2.pdf', '2026-09-05 09:40:32'),
(6, 'SITCOM', '000006', 'Doucouré', 'Daouda', 'ddoucoure@gmail.com', 'Marié(e)', 'cité Hacienda / Marist Dakar', '+221779897452', '1 2154 1981 56847', '2020-03-12', 'CDI', '2150004568', 'B', '2030-01-01', 'Mecanicien', '1792401720_6ad5e138c0de2.pdf', '2026-10-19 09:22:00');

-- --------------------------------------------------------

--
-- Structure de la table `incidents`
--

CREATE TABLE `incidents` (
  `id` int(11) NOT NULL,
  `reference` varchar(50) NOT NULL,
  `employee_id` int(11) DEFAULT NULL,
  `type_incident` enum('Accident du travail','Matériel / Véhicule','Discipline / Insubordination','Sécurité / Vol','Autre') NOT NULL DEFAULT 'Autre',
  `gravite` enum('Faible','Moyen','Élevé','Critique') NOT NULL DEFAULT 'Moyen',
  `lieu` varchar(255) DEFAULT NULL,
  `date_incident` datetime NOT NULL,
  `titre` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `actions_correctives` text DEFAULT NULL,
  `denouement` text DEFAULT NULL,
  `statut` enum('Signolé','En cours','Résolu','Classé sans suite') NOT NULL DEFAULT 'Signolé',
  `impact_financier` decimal(12,2) DEFAULT 0.00,
  `fichier_joint` varchar(255) DEFAULT NULL,
  `fichiers_joints` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `incidents`
--

INSERT INTO `incidents` (`id`, `reference`, `employee_id`, `type_incident`, `gravite`, `lieu`, `date_incident`, `titre`, `description`, `actions_correctives`, `denouement`, `statut`, `impact_financier`, `fichier_joint`, `fichiers_joints`, `created_at`, `updated_at`) VALUES
(4, 'INC-20260910-939', 1, 'Matériel / Véhicule', 'Moyen', 'Route Nationale 1', '2026-09-05 13:24:00', 'Casse Matériel lors d\'une manutention ', 'Lors d’une opération de manutention, une vitre de véhicule a été accidentellement cassée par l’un de nos agents. L’incident est survenu par mégarde lors de la manipulation du matériel. Les dispositions nécessaires seront prises afin d’évaluer les dommages et d’éviter que ce type d’incident ne se reproduise.', 'L’agent concerné a été sensibilisé sur les précautions à prendre lors des opérations de manutention. Les dommages ont été constatés et signalés à la hiérarchie afin de prendre les dispositions nécessaires pour la réparation ou le remplacement de la vitre.', 'Accord trouvé avec le propriétaire du véhicule.', '', 400000.00, '[\"incident_1789039506_6aa293924f005_0.jpg\",\"incident_1789039506_6aa293924f2e1_1.jpg\",\"incident_1789039506_6aa293924f9d6_2.jpg\"]', '[\"incident_1789039506_6aa293924f005_0.jpg\",\"incident_1789039506_6aa293924f2e1_1.jpg\",\"incident_1789039506_6aa293924f9d6_2.jpg\"]', '2026-09-10 11:25:06', '2026-09-10 11:26:45');

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `nom` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `nom`, `email`, `password`, `created_at`) VALUES
(2, 'Ibrahima Coulou', 'ibrahimacoulou2@gmail.com', '$2y$10$FRzPbZT4QUmjs.uCfQS5PejzQdj7ao6YPt2/Wx1ksIau61h3CrGp6', '2026-09-05 13:53:08');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `disputes`
--
ALTER TABLE `disputes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_disputes_employee` (`employee_id`);

--
-- Index pour la table `dispute_documents`
--
ALTER TABLE `dispute_documents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_documents_dispute` (`dispute_id`);

--
-- Index pour la table `dispute_updates`
--
ALTER TABLE `dispute_updates`
  ADD PRIMARY KEY (`id`),
  ADD KEY `dispute_id` (`dispute_id`);

--
-- Index pour la table `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `matricule` (`matricule`),
  ADD UNIQUE KEY `cni` (`cni`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Index pour la table `incidents`
--
ALTER TABLE `incidents`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `reference` (`reference`),
  ADD KEY `fk_incidents_employee` (`employee_id`);

--
-- Index pour la table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `disputes`
--
ALTER TABLE `disputes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `dispute_documents`
--
ALTER TABLE `dispute_documents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `dispute_updates`
--
ALTER TABLE `dispute_updates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `employees`
--
ALTER TABLE `employees`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT pour la table `incidents`
--
ALTER TABLE `incidents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `disputes`
--
ALTER TABLE `disputes`
  ADD CONSTRAINT `fk_disputes_employee` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL;

--
-- Contraintes pour la table `dispute_documents`
--
ALTER TABLE `dispute_documents`
  ADD CONSTRAINT `fk_documents_dispute` FOREIGN KEY (`dispute_id`) REFERENCES `disputes` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `dispute_updates`
--
ALTER TABLE `dispute_updates`
  ADD CONSTRAINT `dispute_updates_ibfk_1` FOREIGN KEY (`dispute_id`) REFERENCES `disputes` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `incidents`
--
ALTER TABLE `incidents`
  ADD CONSTRAINT `fk_incidents_employee` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
