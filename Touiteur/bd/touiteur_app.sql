-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : mar. 07 nov. 2023 à 08:32
-- Version du serveur : 10.4.28-MariaDB
-- Version de PHP : 8.0.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `touiteur.app`
--

-- --------------------------------------------------------

--
-- Structure de la table `abonnementtags`
--

CREATE TABLE `AbonnementTags` (
                                  `utilisateurID` int(11) NOT NULL,
                                  `tagID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Déchargement des données de la table `abonnementtags`
--

INSERT INTO `AbonnementTags` (`utilisateurID`, `tagID`) VALUES
                                                            (1, 1),
                                                            (2, 2),
                                                            (3, 3),
                                                            (4, 4);

-- --------------------------------------------------------

--
-- Structure de la table `evaluations`
--

CREATE TABLE `Evaluations` (
                               `touiteID` int(11) NOT NULL,
                               `utilisateurID` int(11) NOT NULL,
                               `note` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Déchargement des données de la table `evaluations`
--

INSERT INTO `Evaluations` (`touiteID`, `utilisateurID`, `note`) VALUES
                                                                    (1, 2, 8),
                                                                    (1, 3, 7),
                                                                    (2, 1, 5),
                                                                    (3, 4, 9);

-- --------------------------------------------------------

--
-- Structure de la table `images`
--

CREATE TABLE `Images` (
                          `imageID` int(11) NOT NULL,
                          `description` text DEFAULT NULL,
                          `cheminFichier` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Déchargement des données de la table `images`
--

INSERT INTO `Images` (`imageID`, `description`, `cheminFichier`) VALUES
                                                                     (1, 'Image de profil', 'icone_de_profil.jpg'),
                                                                     (2, 'Couverture pour touite', 'couverture_touite.jpg'),
                                                                     (3, 'Illustration de touite', 'illustration_touite.jpg'),
                                                                     (4, 'Photo de paysage', 'photo_paysage.jpg');

-- --------------------------------------------------------

--
-- Structure de la table `suivi`
--

CREATE TABLE `Suivi` (
                         `suivreID` int(11) NOT NULL,
                         `suiviID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Déchargement des données de la table `suivi`
--

INSERT INTO `Suivi` (`suivreID`, `suiviID`) VALUES
                                                (1, 2),
                                                (1, 3),
                                                (2, 3),
                                                (3, 4);

-- --------------------------------------------------------

--
-- Structure de la table `tags`
--

CREATE TABLE `Tags` (
                        `tagID` int(11) NOT NULL,
                        `libelle` varchar(100) DEFAULT NULL,
                        `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Déchargement des données de la table `tags`
--

INSERT INTO `Tags` (`tagID`, `libelle`, `description`) VALUES
                                                           (1, '#ExempleTag', 'Un tag pour des exemples'),
                                                           (2, '#Portfolio', 'Tag lié aux portfolios'),
                                                           (3, '#Accord', 'Tag pour exprimer l’accord'),
                                                           (4, '#Touiteur', 'Le tag officiel de Touiteur');

-- --------------------------------------------------------

--
-- Structure de la table `touites`
--

CREATE TABLE `Touites` (
                           `touiteID` int(11) NOT NULL,
                           `texte` varchar(235) DEFAULT NULL,
                           `datePublication` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Déchargement des données de la table `touites`
--

INSERT INTO `Touites` (`touiteID`, `texte`, `datePublication`) VALUES
                                                                   (1, 'Ceci est mon premier touite !', '2023-11-06 10:00:00'),
                                                                   (2, 'Un autre touite avec un', '2023-11-06 11:00:00'),
                                                                   (3, 'Découvrez mon travail sur mon site', '2023-11-06 12:00:00'),
                                                                   (4, 'Je suis d’accord avec ça', '2023-11-06 13:00:00');

-- --------------------------------------------------------

--
-- Structure de la table `touitesimages`
--

CREATE TABLE `TouitesImages` (
                                 `TouiteID` int(11) NOT NULL,
                                 `ImageID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Déchargement des données de la table `touitesimages`
--

INSERT INTO `TouitesImages` (`TouiteID`, `ImageID`) VALUES
                                                        (2, 2),
                                                        (3, 3);

-- --------------------------------------------------------

--
-- Structure de la table `touitestags`
--

CREATE TABLE `TouitesTags` (
                               `TouiteID` int(11) NOT NULL,
                               `TagID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Déchargement des données de la table `touitestags`
--

INSERT INTO `TouitesTags` (`TouiteID`, `TagID`) VALUES
                                                    (2, 1),
                                                    (3, 2),
                                                    (4, 3);

-- --------------------------------------------------------

--
-- Structure de la table `touitesutilisateurs`
--

CREATE TABLE `TouitesUtilisateurs` (
                                       `TouiteID` int(11) NOT NULL,
                                       `utilisateurID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Déchargement des données de la table `touitesutilisateurs`
--

INSERT INTO `TouitesUtilisateurs` (`TouiteID`, `utilisateurID`) VALUES
                                                                    (1, 4),
                                                                    (2, 3),
                                                                    (4, 1),
                                                                    (3, 2);

-- --------------------------------------------------------

--
-- Structure de la table `utilisateurs`
--

CREATE TABLE `Utilisateurs` (
                                `utilisateurID` int(11) NOT NULL,
                                `nom` varchar(255) DEFAULT NULL,
                                `prenom` varchar(255) DEFAULT NULL,
                                `email` varchar(255) DEFAULT NULL,
                                `mdp` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Déchargement des données de la table `utilisateurs`
--

INSERT INTO `Utilisateurs` (`utilisateurID`, `nom`, `prenom`, `email`, `mdp`) VALUES
                                                                                  (1, 'Dupont', 'Alice', 'alice.dupont@gmail.com', '$2y$10$8xiooHDFoThl5lGciRhuIuk8tfMb7eKsaNO6vaOm7BqD9u./E.Oo.'),
                                                                                  (2, 'Durand', 'Bob', 'bob.durand@hotmail.com', '$2y$10$9II7AtfWq2CmfDX9wh.Wzecy5OFhNKYksZweM1soAH84ZJPHOt09.'),
                                                                                  (3, 'Leroy', 'Charlie', 'charlie.leroy@sfr.com', '$2y$10$vlbqiAvd4rg0iY4Fv60dne3lXrtclwPXhf9guln59SS3Ww..lagim'),
                                                                                  (4, 'Moreau', 'Diana', 'diana.moreau@gmail.com', '$2y$10$8srs7.d6FTXGP0adp3log.U.yaHVJj8dONnZKSLCCqHREi1P383L2'),
                                                                                    (5,'Root','Root','root@gmail.com','$2y$10$pXPoEd/24G30rb.tzhs5G.RfOao81yWfdo6.1e0D7NzBcgnD/LZ0u');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `abonnementtags`
--
ALTER TABLE `Abonnementtags`
    ADD PRIMARY KEY (`utilisateurID`,`tagID`),
  ADD KEY `utilisateurID` (`utilisateurID`),
  ADD KEY `tagID` (`tagID`);

--
-- Index pour la table `evaluations`
--
ALTER TABLE `Evaluations`
    ADD PRIMARY KEY (`touiteID`,`utilisateurID`),
  ADD KEY `touiteID` (`touiteID`),
  ADD KEY `utilisateurID` (`utilisateurID`);

--
-- Index pour la table `images`
--
ALTER TABLE `Images`
    ADD PRIMARY KEY (`imageID`);

--
-- Index pour la table `suivi`
--
ALTER TABLE `Suivi`
    ADD PRIMARY KEY (`suivreID`,`suiviID`),
  ADD KEY `suivreID` (`suivreID`),
  ADD KEY `suiviID` (`suiviID`);

--
-- Index pour la table `tags`
--
ALTER TABLE `Tags`
    ADD PRIMARY KEY (`tagID`);

--
-- Index pour la table `touites`
--
ALTER TABLE `Touites`
    ADD PRIMARY KEY (`touiteID`);

--
-- Index pour la table `touitesimages`
--
ALTER TABLE `TouitesImages`
    ADD PRIMARY KEY (`TouiteID`,`ImageID`),
  ADD KEY `TouiteID` (`TouiteID`),
  ADD KEY `ImageID` (`ImageID`);

--
-- Index pour la table `touitestags`
--
ALTER TABLE `TouitesTags`
    ADD PRIMARY KEY (`TouiteID`,`TagID`),
  ADD KEY `TouiteID` (`TouiteID`),
  ADD KEY `TagID` (`TagID`);

--
-- Index pour la table `touitesutilisateurs`
--
ALTER TABLE `TouitesUtilisateurs`
    ADD PRIMARY KEY (`TouiteID`,`utilisateurID`),
  ADD KEY `TouiteID` (`TouiteID`),
  ADD KEY `TagID` (`utilisateurID`);

--
-- Index pour la table `utilisateurs`
--
ALTER TABLE `Utilisateurs`
    ADD PRIMARY KEY (`utilisateurID`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `images`
--
ALTER TABLE `Images`
    MODIFY `imageID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `tags`
--
ALTER TABLE `Tags`
    MODIFY `tagID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `touites`
--
ALTER TABLE `Touites`
    MODIFY `touiteID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `utilisateurs`
--
ALTER TABLE `Utilisateurs`
    MODIFY `utilisateurID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `abonnementtags`
--
ALTER TABLE `Abonnementtags`
    ADD CONSTRAINT `abonnementtags_ibfk_1` FOREIGN KEY (`utilisateurID`) REFERENCES `utilisateurs` (`utilisateurID`),
  ADD CONSTRAINT `abonnementtags_ibfk_2` FOREIGN KEY (`tagID`) REFERENCES `tags` (`tagID`);

--
-- Contraintes pour la table `evaluations`
--
ALTER TABLE `Evaluations`
    ADD CONSTRAINT `evaluations_ibfk_1` FOREIGN KEY (`touiteID`) REFERENCES `touites` (`touiteID`),
  ADD CONSTRAINT `evaluations_ibfk_2` FOREIGN KEY (`utilisateurID`) REFERENCES `utilisateurs` (`utilisateurID`);

--
-- Contraintes pour la table `suivi`
--
ALTER TABLE `Suivi`
    ADD CONSTRAINT `suivi_ibfk_1` FOREIGN KEY (`suivreID`) REFERENCES `utilisateurs` (`utilisateurID`),
  ADD CONSTRAINT `suivi_ibfk_2` FOREIGN KEY (`suiviID`) REFERENCES `utilisateurs` (`utilisateurID`);

--
-- Contraintes pour la table `touitesimages`
--
ALTER TABLE `TouitesImages`
    ADD CONSTRAINT `touitesimages_ibfk_1` FOREIGN KEY (`TouiteID`) REFERENCES `touites` (`touiteID`),
  ADD CONSTRAINT `touitesimages_ibfk_2` FOREIGN KEY (`ImageID`) REFERENCES `images` (`imageID`);

--
-- Contraintes pour la table `touitestags`
--
ALTER TABLE `TouitesTags`
    ADD CONSTRAINT `touitestags_ibfk_1` FOREIGN KEY (`TouiteID`) REFERENCES `touites` (`touiteID`),
  ADD CONSTRAINT `touitestags_ibfk_2` FOREIGN KEY (`TagID`) REFERENCES `tags` (`tagID`);

--
-- Contraintes pour la table `touitesutilisateurs`
--
ALTER TABLE `TouitesUtilisateurs`
    ADD CONSTRAINT `touitesutilisateurs_ibfk_1` FOREIGN KEY (`TouiteID`) REFERENCES `touites` (`touiteID`),
  ADD CONSTRAINT `touitesutilisateurs_ibfk_2` FOREIGN KEY (`utilisateurID`) REFERENCES `utilisateurs` (`utilisateurID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;