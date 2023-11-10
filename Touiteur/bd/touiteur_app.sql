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

CREATE TABLE `abonnementtags` (
                                  `utilisateurID` int(11) NOT NULL,
                                  `tagID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Déchargement des données de la table `abonnementtags`
--

INSERT INTO `abonnementtags` (`utilisateurID`, `tagID`) VALUES
                                                            (1, 1),
                                                            (2, 2),
                                                            (3, 3),
                                                            (4, 4);

-- --------------------------------------------------------

--
-- Structure de la table `evaluations`
--

CREATE TABLE `evaluations` (
                               `touiteID` int(11) NOT NULL,
                               `utilisateurID` int(11) NOT NULL,
                               `note` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Déchargement des données de la table `evaluations`
--

INSERT INTO `evaluations` (`touiteID`, `utilisateurID`, `note`) VALUES
                                                                    (1, 2, 8),
                                                                    (1, 3, 7),
                                                                    (2, 1, 5),
                                                                    (3, 4, 9);

-- --------------------------------------------------------

--
-- Structure de la table `images`
--

CREATE TABLE `images` (
                          `imageID` int(11) NOT NULL,
                          `description` text DEFAULT NULL,
                          `cheminFichier` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Déchargement des données de la table `images`
--

INSERT INTO `images` (`imageID`, `description`, `cheminFichier`) VALUES
                                                                     (1, 'Image de profil', 'icone_de_profil.jpg'),
                                                                     (2, 'Couverture pour touite', 'couverture_touite.jpg'),
                                                                     (3, 'Illustration de touite', 'illustration_touite.jpg'),
                                                                     (4, 'Photo de paysage', 'photo_paysage.jpg');

-- --------------------------------------------------------

--
-- Structure de la table `suivi`
--

CREATE TABLE `suivi` (
                         `suivreID` int(11) NOT NULL,
                         `suiviID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Déchargement des données de la table `suivi`
--

INSERT INTO `suivi` (`suivreID`, `suiviID`) VALUES
                                                (1, 2),
                                                (1, 3),
                                                (2, 3),
                                                (3, 4);

-- --------------------------------------------------------

--
-- Structure de la table `tags`
--

CREATE TABLE `tags` (
                        `tagID` int(11) NOT NULL,
                        `libelle` varchar(100) DEFAULT NULL,
                        `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Déchargement des données de la table `tags`
--

INSERT INTO `tags` (`tagID`, `libelle`, `description`) VALUES
                                                           (1, '#ExempleTag', 'Un tag pour des exemples'),
                                                           (2, '#Portfolio', 'Tag lié aux portfolios'),
                                                           (3, '#Accord', 'Tag pour exprimer l’accord'),
                                                           (4, '#Touiteur', 'Le tag officiel de Touiteur');

-- --------------------------------------------------------

--
-- Structure de la table `touites`
--

CREATE TABLE `touites` (
                           `touiteID` int(11) NOT NULL,
                           `texte` varchar(235) DEFAULT NULL,
                           `datePublication` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Déchargement des données de la table `touites`
--

INSERT INTO `touites` (`touiteID`, `texte`, `datePublication`) VALUES
                                                                   (1, 'Ceci est mon premier touite !', '2023-11-06 10:00:00'),
                                                                   (2, 'Un autre touite avec un', '2023-11-06 11:00:00'),
                                                                   (3, 'Découvrez mon travail sur mon site', '2023-11-06 12:00:00'),
                                                                   (4, 'Je suis d’accord avec ça', '2023-11-06 13:00:00');

-- --------------------------------------------------------

--
-- Structure de la table `touitesimages`
--

CREATE TABLE `touitesimages` (
                                 `TouiteID` int(11) NOT NULL,
                                 `ImageID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Déchargement des données de la table `touitesimages`
--

INSERT INTO `touitesimages` (`TouiteID`, `ImageID`) VALUES
                                                        (2, 2),
                                                        (3, 3);

-- --------------------------------------------------------

--
-- Structure de la table `touitestags`
--

CREATE TABLE `touitestags` (
                               `TouiteID` int(11) NOT NULL,
                               `TagID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Déchargement des données de la table `touitestags`
--

INSERT INTO `touitestags` (`TouiteID`, `TagID`) VALUES
                                                    (2, 1),
                                                    (3, 2),
                                                    (4, 3);

-- --------------------------------------------------------

--
-- Structure de la table `touitesutilisateurs`
--

CREATE TABLE `touitesutilisateurs` (
                                       `TouiteID` int(11) NOT NULL,
                                       `utilisateurID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Déchargement des données de la table `touitesutilisateurs`
--

INSERT INTO `touitesutilisateurs` (`TouiteID`, `utilisateurID`) VALUES
                                                                    (1, 4),
                                                                    (2, 3),
                                                                    (4, 1),
                                                                    (3, 2);

-- --------------------------------------------------------

--
-- Structure de la table `utilisateurs`
--

CREATE TABLE `utilisateurs` (
                                `utilisateurID` int(11) NOT NULL,
                                `nom` varchar(255) DEFAULT NULL,
                                `prenom` varchar(255) DEFAULT NULL,
                                `email` varchar(255) DEFAULT NULL,
                                `mdp` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Déchargement des données de la table `utilisateurs`
--

INSERT INTO `utilisateurs` (`utilisateurID`, `nom`, `prenom`, `email`, `mdp`) VALUES
                                                                                  (1, 'Dupont', 'Alice', 'alice.dupont@gmail.com', 'jaimemonchat'),
                                                                                  (2, 'Durand', 'Bob', 'bob.durand@hotmail.com', 'durandbob'),
                                                                                  (3, 'Leroy', 'Charlie', 'charlie.leroy@sfr.com', 'azertyuiop'),
                                                                                  (4, 'Moreau', 'Diana', 'diana.moreau@gmail.com', 'diana357');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `abonnementtags`
--
ALTER TABLE `abonnementtags`
    ADD PRIMARY KEY (`utilisateurID`,`tagID`),
  ADD KEY `utilisateurID` (`utilisateurID`),
  ADD KEY `tagID` (`tagID`);

--
-- Index pour la table `evaluations`
--
ALTER TABLE `evaluations`
    ADD PRIMARY KEY (`touiteID`,`utilisateurID`),
  ADD KEY `touiteID` (`touiteID`),
  ADD KEY `utilisateurID` (`utilisateurID`);

--
-- Index pour la table `images`
--
ALTER TABLE `images`
    ADD PRIMARY KEY (`imageID`);

--
-- Index pour la table `suivi`
--
ALTER TABLE `suivi`
    ADD PRIMARY KEY (`suivreID`,`suiviID`),
  ADD KEY `suivreID` (`suivreID`),
  ADD KEY `suiviID` (`suiviID`);

--
-- Index pour la table `tags`
--
ALTER TABLE `tags`
    ADD PRIMARY KEY (`tagID`);

--
-- Index pour la table `touites`
--
ALTER TABLE `touites`
    ADD PRIMARY KEY (`touiteID`);

--
-- Index pour la table `touitesimages`
--
ALTER TABLE `touitesimages`
    ADD PRIMARY KEY (`TouiteID`,`ImageID`),
  ADD KEY `TouiteID` (`TouiteID`),
  ADD KEY `ImageID` (`ImageID`);

--
-- Index pour la table `touitestags`
--
ALTER TABLE `touitestags`
    ADD PRIMARY KEY (`TouiteID`,`TagID`),
  ADD KEY `TouiteID` (`TouiteID`),
  ADD KEY `TagID` (`TagID`);

--
-- Index pour la table `touitesutilisateurs`
--
ALTER TABLE `touitesutilisateurs`
    ADD PRIMARY KEY (`TouiteID`,`utilisateurID`),
  ADD KEY `TouiteID` (`TouiteID`),
  ADD KEY `TagID` (`utilisateurID`);

--
-- Index pour la table `utilisateurs`
--
ALTER TABLE `utilisateurs`
    ADD PRIMARY KEY (`utilisateurID`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `images`
--
ALTER TABLE `images`
    MODIFY `imageID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `tags`
--
ALTER TABLE `tags`
    MODIFY `tagID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `touites`
--
ALTER TABLE `touites`
    MODIFY `touiteID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `utilisateurs`
--
ALTER TABLE `utilisateurs`
    MODIFY `utilisateurID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `abonnementtags`
--
ALTER TABLE `abonnementtags`
    ADD CONSTRAINT `abonnementtags_ibfk_1` FOREIGN KEY (`utilisateurID`) REFERENCES `utilisateurs` (`utilisateurID`),
  ADD CONSTRAINT `abonnementtags_ibfk_2` FOREIGN KEY (`tagID`) REFERENCES `tags` (`tagID`);

--
-- Contraintes pour la table `evaluations`
--
ALTER TABLE `evaluations`
    ADD CONSTRAINT `evaluations_ibfk_1` FOREIGN KEY (`touiteID`) REFERENCES `touites` (`touiteID`),
  ADD CONSTRAINT `evaluations_ibfk_2` FOREIGN KEY (`utilisateurID`) REFERENCES `utilisateurs` (`utilisateurID`);

--
-- Contraintes pour la table `suivi`
--
ALTER TABLE `suivi`
    ADD CONSTRAINT `suivi_ibfk_1` FOREIGN KEY (`suivreID`) REFERENCES `utilisateurs` (`utilisateurID`),
  ADD CONSTRAINT `suivi_ibfk_2` FOREIGN KEY (`suiviID`) REFERENCES `utilisateurs` (`utilisateurID`);

--
-- Contraintes pour la table `touitesimages`
--
ALTER TABLE `touitesimages`
    ADD CONSTRAINT `touitesimages_ibfk_1` FOREIGN KEY (`TouiteID`) REFERENCES `touites` (`touiteID`),
  ADD CONSTRAINT `touitesimages_ibfk_2` FOREIGN KEY (`ImageID`) REFERENCES `images` (`imageID`);

--
-- Contraintes pour la table `touitestags`
--
ALTER TABLE `touitestags`
    ADD CONSTRAINT `touitestags_ibfk_1` FOREIGN KEY (`TouiteID`) REFERENCES `touites` (`touiteID`),
  ADD CONSTRAINT `touitestags_ibfk_2` FOREIGN KEY (`TagID`) REFERENCES `tags` (`tagID`);

--
-- Contraintes pour la table `touitesutilisateurs`
--
ALTER TABLE `touitesutilisateurs`
    ADD CONSTRAINT `touitesutilisateurs_ibfk_1` FOREIGN KEY (`TouiteID`) REFERENCES `touites` (`touiteID`),
  ADD CONSTRAINT `touitesutilisateurs_ibfk_2` FOREIGN KEY (`utilisateurID`) REFERENCES `utilisateurs` (`utilisateurID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;