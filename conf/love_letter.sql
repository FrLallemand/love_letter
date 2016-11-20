-- phpMyAdmin SQL Dump
-- version 4.6.4
-- https://www.phpmyadmin.net/
--
-- Client :  localhost
-- Généré le :  Dim 20 Novembre 2016 à 09:33
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
-- Structure de la table `partie`
--

CREATE TABLE `partie` (
  `idpartie` int(11) NOT NULL,
  `joueur_1` int(11) NOT NULL,
  `joueur_2` int(11) NOT NULL,
  `joueur_3` int(11) NOT NULL,
  `joueur_4` int(11) NOT NULL,
  `joueurs_maximum` int(11) NOT NULL DEFAULT '4',
  `joueurs_actuel` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Index pour les tables exportées
--

--
-- Index pour la table `partie`
--
ALTER TABLE `partie`
  ADD PRIMARY KEY (`idpartie`);

--
-- AUTO_INCREMENT pour les tables exportées
--

--
-- AUTO_INCREMENT pour la table `partie`
--
ALTER TABLE `partie`
  MODIFY `idpartie` int(11) NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
