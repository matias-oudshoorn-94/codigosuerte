-- --------------------------------------------------------
-- Host:                         jose-aguilar.com
-- Versión del servidor:         5.1.70-cll - MySQL Community Server (GPL)
-- SO del servidor:              unknown-linux-gnu
-- HeidiSQL Versión:             8.1.0.4545
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

-- Volcando estructura para tabla josea902_scripts.username_availablity
CREATE TABLE IF NOT EXISTS `username_availablity` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(60) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

-- Volcando datos para la tabla josea902_scripts.username_availablity: 2 rows
DELETE FROM `username_availablity`;
/*!40000 ALTER TABLE `username_availablity` DISABLE KEYS */;
INSERT INTO `username_availablity` (`id`, `username`) VALUES
	(1, 'jose'),
	(2, 'maria');
/*!40000 ALTER TABLE `username_availablity` ENABLE KEYS */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
