-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost
-- Généré le : lun. 13 nov. 2023 à 17:54
-- Version du serveur : 5.5.68-MariaDB
-- Version de PHP : 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `dziezuk2u`
--

-- --------------------------------------------------------

--
-- Structure de la table `touites`
--

CREATE TABLE `touites` (
  `touiteID` int(11) NOT NULL,
  `texte` varchar(235) DEFAULT NULL,
  `datePublication` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Déchargement des données de la table `touites`
--

INSERT INTO `touites` (`touiteID`, `texte`, `datePublication`) VALUES
(1, 'Ceci est mon premier touite !', '2023-11-06 10:00:00'),
(2, 'Un autre touite avec un', '2023-11-06 11:00:00'),
(3, 'Découvrez mon travail sur mon site', '2023-11-06 12:00:00'),
(6, 'Je suis d&#39;accord #Accord', '2023-11-13 13:31:22');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `touites`
--
ALTER TABLE `touites`
  ADD PRIMARY KEY (`touiteID`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `touites`
--
ALTER TABLE `touites`
  MODIFY `touiteID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
