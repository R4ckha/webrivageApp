-- phpMyAdmin SQL Dump
-- version 4.9.2
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3308
-- Généré le :  Dim 09 fév. 2020 à 20:25
-- Version du serveur :  8.0.18
-- Version de PHP :  7.3.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données :  `imagiluxcyroot`
--

-- --------------------------------------------------------

--
-- Structure de la table `discount_rules`
--

DROP TABLE IF EXISTS `discount_rules`;
CREATE TABLE IF NOT EXISTS `discount_rules` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `rule_expression` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `discount_percent` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `discount_rules`
--

INSERT INTO `discount_rules` (`id`, `rule_expression`, `discount_percent`) VALUES
(1, 'product.type === \'electomenager\' and product.price > 500 ? 0.1 : 0', 10),
(2, 'product.type === \'montre_connectee\' and product.price > 200 and date(\'now\') >= date(\'2020-02-08\') and date(\'now\') <= date(\'2020-02-30\') ? 0.2 : 0', 20);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
