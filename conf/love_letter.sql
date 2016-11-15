-- phpMyAdmin SQL Dump
-- version 4.6.4
-- https://www.phpmyadmin.net/
--
-- Client :  localhost
-- Généré le :  Mar 15 Novembre 2016 à 13:03
-- Version du serveur :  10.1.18-MariaDB
-- Version de PHP :  5.6.27

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données :  `love_letter`
--

-- --------------------------------------------------------

--
-- Structure de la table `joueur`
--

CREATE TABLE `joueur` (
  `idjoueur` int(11) NOT NULL,
  `nom` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `partie`
--

CREATE TABLE `partie` (
  `idpartie` int(11) NOT NULL,
  `joueur_1` int(11) NOT NULL,
  `joueur_2` int(11) NOT NULL,
  `joueur_3` int(11) NOT NULL,
  `joueur_4` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `tas`
--

CREATE TABLE `tas` (
  `idpartie` int(11) NOT NULL,
  `proprietaire` int(11) NOT NULL DEFAULT '-1',
  `nom` varchar(25) NOT NULL,
  `description` varchar(500) NOT NULL,
  `niveau` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Index pour les tables exportées
--

--
-- Index pour la table `joueur`
--
ALTER TABLE `joueur`
  ADD PRIMARY KEY (`idjoueur`);

--
-- Index pour la table `partie`
--
ALTER TABLE `partie`
  ADD PRIMARY KEY (`idpartie`),
  ADD KEY `idpartie` (`idpartie`);

--
-- Index pour la table `tas`
--
ALTER TABLE `tas`
  ADD KEY `idpartie` (`idpartie`);

--
-- Contraintes pour les tables exportées
--

--
-- Contraintes pour la table `partie`
--
ALTER TABLE `partie`
  ADD CONSTRAINT `joueur_1 ` FOREIGN KEY (`idpartie`) REFERENCES `joueur` (`idjoueur`),
  ADD CONSTRAINT `joueur_2` FOREIGN KEY (`idpartie`) REFERENCES `joueur` (`idjoueur`),
  ADD CONSTRAINT `joueur_3` FOREIGN KEY (`idpartie`) REFERENCES `joueur` (`idjoueur`),
  ADD CONSTRAINT `joueur_4` FOREIGN KEY (`idpartie`) REFERENCES `joueur` (`idjoueur`);

--
-- Contraintes pour la table `tas`
--
ALTER TABLE `tas`
  ADD CONSTRAINT `idpartie` FOREIGN KEY (`idpartie`) REFERENCES `partie` (`idpartie`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
