-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : mer. 20 mai 2026 à 19:18
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
-- Base de données : `mjs_personnel_db`
--

-- --------------------------------------------------------

--
-- Structure de la table `agents`
--

CREATE TABLE `agents` (
  `id_agent` int(11) NOT NULL,
  `matricule_solde` varchar(20) NOT NULL,
  `cni` varchar(20) NOT NULL,
  `prenom` varchar(75) NOT NULL,
  `nom` varchar(50) NOT NULL,
  `date_naissance` date NOT NULL,
  `lieu_naissance` varchar(100) NOT NULL,
  `sexe` enum('M','F') NOT NULL,
  `situation_matrimoniale` enum('Célibataire','Marié(e)','Divorcé(e)','Veuf(ve)') NOT NULL,
  `telephone` varchar(20) NOT NULL,
  `email_professionnel` varchar(100) DEFAULT NULL,
  `email_personnel` varchar(100) DEFAULT NULL,
  `adresse` varchar(255) NOT NULL,
  `statut_agent` enum('Fonctionnaire','Contractuel','Décisionnaire','Stagiaire') NOT NULL,
  `date_integration_fonction_publique` date DEFAULT NULL,
  `date_entree_ministere` date NOT NULL,
  `id_grade` int(11) DEFAULT NULL,
  `id_structure_actuelle` int(11) DEFAULT NULL,
  `statut_activite` enum('Actif','Détaché','Disponibilité','Retraité') DEFAULT 'Actif',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `agents`
--

INSERT INTO `agents` (`id_agent`, `matricule_solde`, `cni`, `prenom`, `nom`, `date_naissance`, `lieu_naissance`, `sexe`, `situation_matrimoniale`, `telephone`, `email_professionnel`, `email_personnel`, `adresse`, `statut_agent`, `date_integration_fonction_publique`, `date_entree_ministere`, `id_grade`, `id_structure_actuelle`, `statut_activite`, `created_at`, `updated_at`) VALUES
(2, 'massefall', '1234198101234', 'Masse', 'Fall', '1981-03-14', 'Dakar', 'M', 'Marié(e)', '775572030', 'masse.fall@sport.gouv.sn', NULL, 'Cité Douanes golf sud', 'Fonctionnaire', NULL, '2026-05-20', NULL, NULL, 'Actif', '2026-05-20 11:13:13', '2026-05-20 11:13:13'),
(3, 'MTR-001', '1234198500001', 'Omar', 'Ly', '1985-05-12', 'Dakar', 'M', 'Marié(e)', '777951058', 'omar.ly@sport.gouv.sn', NULL, 'Dakar', 'Fonctionnaire', NULL, '2026-01-15', NULL, NULL, 'Actif', '2026-05-20 12:56:10', '2026-05-20 12:56:10'),
(4, 'MTR-002', '2234199000002', 'Absa', 'Faye', '1990-08-22', 'Thiès', 'F', 'Célibataire', '773042800', 'absa.faye@sport.gouv.sn', NULL, 'Dakar', 'Contractuel', NULL, '2026-02-10', NULL, NULL, 'Actif', '2026-05-20 12:56:10', '2026-05-20 12:56:10'),
(5, 'MTR-003', '1234198800003', 'Mouhamed', 'Fall', '1988-11-03', 'Saint-Louis', 'M', 'Marié(e)', '772566854', 'mouhamed.fall@sport.gouv.sn', NULL, 'Dakar', 'Fonctionnaire', NULL, '2026-03-01', NULL, NULL, 'Actif', '2026-05-20 12:56:10', '2026-05-20 12:56:10'),
(6, 'MTR-004', '1234199200004', 'Souleymane', 'Fall', '1992-02-14', 'Dakar', 'M', 'Célibataire', '772147893', 'souleymane.fall@sport.gouv.sn', NULL, 'Dakar', '', NULL, '2026-03-15', NULL, NULL, 'Actif', '2026-05-20 12:56:10', '2026-05-20 12:56:10'),
(7, 'MTR-005', '1234198700005', 'Sidy', 'Ndiaye', '1987-04-19', 'Kaolack', 'M', 'Marié(e)', '775897123', 'sidy.ndiaye@sport.gouv.sn', NULL, 'Dakar', 'Fonctionnaire', NULL, '2026-04-01', NULL, NULL, 'Actif', '2026-05-20 12:56:10', '2026-05-20 12:56:10'),
(8, 'MTR-006', '2234199400006', 'Awa Gueye', 'Fall', '1994-07-25', 'Dakar', 'F', 'Célibataire', '765987452', 'awa.fall@sport.gouv.sn', NULL, 'Dakar', 'Contractuel', NULL, '2026-04-12', NULL, NULL, 'Actif', '2026-05-20 12:56:10', '2026-05-20 12:56:10'),
(9, 'MTR-007', '1123456789012', 'Nogaye', 'SARR', '1981-01-01', 'Dakar', 'F', 'Marié(e)', '712562145', 'nogaye.sarr@sport.gouv.sn', NULL, '502, PAI Golf Sud', 'Stagiaire', NULL, '2026-02-01', NULL, NULL, 'Actif', '2026-05-20 13:25:42', '2026-05-20 13:25:42');

-- --------------------------------------------------------

--
-- Structure de la table `conges`
--

CREATE TABLE `conges` (
  `id_conge` int(11) NOT NULL,
  `id_agent` int(11) NOT NULL,
  `type_conge` enum('Annuel','Maladie','Maternité','Sans Solde','Exceptionnel') NOT NULL,
  `date_debut` date NOT NULL,
  `date_fin` date NOT NULL,
  `motif` text DEFAULT NULL,
  `statut_demande` enum('En attente','Approuvé','Rejeté') DEFAULT 'En attente',
  `date_decision` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `grades`
--

CREATE TABLE `grades` (
  `id_grade` int(11) NOT NULL,
  `libelle_grade` varchar(100) NOT NULL,
  `hierarchie` enum('A1','A2','B','C','D') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `historique_affectations`
--

CREATE TABLE `historique_affectations` (
  `id_affectation` int(11) NOT NULL,
  `id_agent` int(11) NOT NULL,
  `id_structure` int(11) NOT NULL,
  `poste_occupe` varchar(100) NOT NULL,
  `date_debut` date NOT NULL,
  `date_fin` date DEFAULT NULL,
  `acte_administratif` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `structures`
--

CREATE TABLE `structures` (
  `id_structure` int(11) NOT NULL,
  `nom_structure` varchar(150) NOT NULL,
  `type_structure` enum('Cabinet','Direction Générale','Direction Centrale','Service Interne','IRJS','CDEPS') NOT NULL,
  `telephone_structure` varchar(20) DEFAULT NULL,
  `localisation` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `utilisateurs`
--

CREATE TABLE `utilisateurs` (
  `id_utilisateur` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('SuperAdmin','DAGE_RH','Consultant','Agent') NOT NULL DEFAULT 'Agent',
  `id_agent` int(11) DEFAULT NULL,
  `actif` tinyint(1) DEFAULT 1,
  `last_login` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `utilisateurs`
--

INSERT INTO `utilisateurs` (`id_utilisateur`, `username`, `password_hash`, `role`, `id_agent`, `actif`, `last_login`, `created_at`) VALUES
(3, 'massefall', '$2y$10$46/7gB6pU.HEvk07nBrjx.gjjgsiW4p3kLBoqgYh0JTrFwn9nqgjC', 'SuperAdmin', 2, 1, NULL, '2026-05-20 11:13:13'),
(4, 'omar_ly', '$2y$10$fVwJk78lR743I4kCiwL1E.m8fG6kGv3xT/QZgU0XgU6Bq97gWwM2e', 'DAGE_RH', 3, 1, NULL, '2026-05-20 12:56:10'),
(5, 'absa_faye', '$2y$10$fVwJk78lR743I4kCiwL1E.m8fG6kGv3xT/QZgU0XgU6Bq97gWwM2e', '', 4, 1, NULL, '2026-05-20 12:56:10'),
(6, 'mouhamed_fall', '$2y$10$fVwJk78lR743I4kCiwL1E.m8fG6kGv3xT/QZgU0XgU6Bq97gWwM2e', 'Agent', 5, 1, NULL, '2026-05-20 12:56:10'),
(7, 'souleymane_fall', '$2y$10$fVwJk78lR743I4kCiwL1E.m8fG6kGv3xT/QZgU0XgU6Bq97gWwM2e', 'Agent', 6, 1, NULL, '2026-05-20 12:56:10'),
(8, 'MTR-007', '$2y$10$TFgg.bfJh7eBVJa7uzqCA.2tsAgrJBAkTcxr0XVWix0jVxKSxENem', 'Agent', 9, 1, NULL, '2026-05-20 13:25:42');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `agents`
--
ALTER TABLE `agents`
  ADD PRIMARY KEY (`id_agent`),
  ADD UNIQUE KEY `matricule_solde` (`matricule_solde`),
  ADD UNIQUE KEY `cni` (`cni`),
  ADD UNIQUE KEY `email_professionnel` (`email_professionnel`),
  ADD KEY `fk_agents_grade` (`id_grade`),
  ADD KEY `fk_agents_structure` (`id_structure_actuelle`);

--
-- Index pour la table `conges`
--
ALTER TABLE `conges`
  ADD PRIMARY KEY (`id_conge`),
  ADD KEY `fk_conges_agents` (`id_agent`);

--
-- Index pour la table `grades`
--
ALTER TABLE `grades`
  ADD PRIMARY KEY (`id_grade`);

--
-- Index pour la table `historique_affectations`
--
ALTER TABLE `historique_affectations`
  ADD PRIMARY KEY (`id_affectation`),
  ADD KEY `fk_hist_agents` (`id_agent`),
  ADD KEY `fk_hist_structures` (`id_structure`);

--
-- Index pour la table `structures`
--
ALTER TABLE `structures`
  ADD PRIMARY KEY (`id_structure`);

--
-- Index pour la table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  ADD PRIMARY KEY (`id_utilisateur`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `fk_utilisateurs_agents` (`id_agent`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `agents`
--
ALTER TABLE `agents`
  MODIFY `id_agent` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT pour la table `conges`
--
ALTER TABLE `conges`
  MODIFY `id_conge` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `grades`
--
ALTER TABLE `grades`
  MODIFY `id_grade` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `historique_affectations`
--
ALTER TABLE `historique_affectations`
  MODIFY `id_affectation` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `structures`
--
ALTER TABLE `structures`
  MODIFY `id_structure` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  MODIFY `id_utilisateur` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `agents`
--
ALTER TABLE `agents`
  ADD CONSTRAINT `fk_agents_grade` FOREIGN KEY (`id_grade`) REFERENCES `grades` (`id_grade`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_agents_structure` FOREIGN KEY (`id_structure_actuelle`) REFERENCES `structures` (`id_structure`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Contraintes pour la table `conges`
--
ALTER TABLE `conges`
  ADD CONSTRAINT `fk_conges_agents` FOREIGN KEY (`id_agent`) REFERENCES `agents` (`id_agent`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `historique_affectations`
--
ALTER TABLE `historique_affectations`
  ADD CONSTRAINT `fk_hist_agents` FOREIGN KEY (`id_agent`) REFERENCES `agents` (`id_agent`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_hist_structures` FOREIGN KEY (`id_structure`) REFERENCES `structures` (`id_structure`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  ADD CONSTRAINT `fk_utilisateurs_agents` FOREIGN KEY (`id_agent`) REFERENCES `agents` (`id_agent`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
