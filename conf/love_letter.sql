-- phpMyAdmin SQL Dump
-- version 4.6.4
-- https://www.phpmyadmin.net/
--
-- Client :  localhost
-- Généré le :  Mar 29 Novembre 2016 à 18:39
-- Version du serveur :  10.1.19-MariaDB
-- Version de PHP :  7.0.13

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
-- Structure de la table `carte`
--

CREATE TABLE `carte` (
  `idcarte` int(11) NOT NULL,
  `proprietaire` int(11) NOT NULL,
  `nom` varchar(50) NOT NULL,
  `description` varchar(150) NOT NULL,
  `niveau` int(11) NOT NULL,
  `visible` tinyint(1) NOT NULL DEFAULT '0',
  `chemin_image` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `joueur`
--

CREATE TABLE `joueur` (
  `idjoueur` int(11) NOT NULL,
  `nom` varchar(50) NOT NULL,
  `idpartie` int(11) DEFAULT NULL,
  `actions` text NOT NULL,
  `notifications` text NOT NULL,
  `elimine` tinyint(1) NOT NULL DEFAULT '0',
  `invulnerable` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `partie`
--

CREATE TABLE `partie` (
  `idpartie` int(11) NOT NULL,
  `joueurs` text NOT NULL,
  `joueur_actuel` int(11) NOT NULL,
  `tour_actuel` int(11) NOT NULL DEFAULT '1',
  `joueurs_presents` int(11) NOT NULL,
  `joueurs_maximum` int(11) NOT NULL,
  `updated_at` datetime NOT NULL,
  `created_at` datetime NOT NULL,
  `pioche` int(11) NOT NULL,
  `finie` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `pioche`
--

CREATE TABLE `pioche` (
  `idpioche` int(11) NOT NULL,
  `haut` int(11) NOT NULL DEFAULT '1',
  `carte_1` int(11) NOT NULL DEFAULT '-1',
  `carte_2` int(11) NOT NULL DEFAULT '-1',
  `carte_3` int(11) NOT NULL DEFAULT '-1',
  `carte_4` int(11) NOT NULL DEFAULT '-1',
  `carte_5` int(11) NOT NULL DEFAULT '-1',
  `carte_6` int(11) NOT NULL DEFAULT '-1',
  `carte_7` int(11) NOT NULL DEFAULT '-1',
  `carte_8` int(11) NOT NULL DEFAULT '-1',
  `carte_9` int(11) NOT NULL DEFAULT '-1',
  `carte_10` int(11) NOT NULL DEFAULT '-1',
  `carte_11` int(11) NOT NULL DEFAULT '-1',
  `carte_12` int(11) NOT NULL DEFAULT '-1',
  `carte_13` int(11) NOT NULL DEFAULT '-1',
  `carte_14` int(11) NOT NULL DEFAULT '-1',
  `carte_15` int(11) NOT NULL DEFAULT '-1',
  `carte_16` int(11) NOT NULL DEFAULT '-1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Index pour les tables exportées
--

--
-- Index pour la table `carte`
--
ALTER TABLE `carte`
  ADD PRIMARY KEY (`idcarte`);

--
-- Index pour la table `joueur`
--
ALTER TABLE `joueur`
  ADD PRIMARY KEY (`idjoueur`);

--
-- Index pour la table `partie`
--
ALTER TABLE `partie`
  ADD PRIMARY KEY (`idpartie`);

--
-- Index pour la table `pioche`
--
ALTER TABLE `pioche`
  ADD PRIMARY KEY (`idpioche`);

--
-- AUTO_INCREMENT pour les tables exportées
--

--
-- AUTO_INCREMENT pour la table `carte`
--
ALTER TABLE `carte`
  MODIFY `idcarte` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `joueur`
--
ALTER TABLE `joueur`
  MODIFY `idjoueur` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `partie`
--
ALTER TABLE `partie`
  MODIFY `idpartie` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `pioche`
--
ALTER TABLE `pioche`
  MODIFY `idpioche` int(11) NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
"{\"source\":\"utilisation_baron\",\"content\":{\"joueur_source\":\"kh\",\"cartes_joueur\":[\"\\\/images\\\/Garde.jpg\"],\"niveau_joueur\":1,\"cartes_adversaire\":[\"\\\/images\\\/Garde.jpg\"],\"niveau_adversaire\":1,\"message\":\"\\u00c9galit\\u00e9\"}}"

["source\":\"utilisation_baron\",\"content\":{\"joueur_source\":\"kh\",\"cartes_joueur\":[\"\\\/images\\\/Garde.jpg\"],\"niveau_joueur\":1,\"cartes_adversaire\":[\"\\\/images\\\/Garde.jpg\"],\"niveau_adversaire\":1,\"message\":\"\\u00c9galit\\u00e9\"}"]
