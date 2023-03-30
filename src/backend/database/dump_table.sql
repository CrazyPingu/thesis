SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";
-- --------------------------------------------------------
/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */
;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */
;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */
;
/*!40101 SET NAMES utf8mb4 */
;
-- --------------------------------------------------------
--
-- Table structure for table `tipologia`
--

CREATE TABLE IF NOT EXISTS `tipologia` (
  `idTipologia` int(11) NOT NULL AUTO_INCREMENT,
  `tipo` varchar(50) NOT NULL UNIQUE,
  PRIMARY KEY (`idTipologia`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;
-- --------------------------------------------------------
--
-- Table structure for table `punto_di_interesse`
--

CREATE TABLE IF NOT EXISTS `punto_di_interesse` (
  `objectid` int(11) NOT NULL,
  `id_poi` varchar(36) NOT NULL,
  `descrizione` varchar(255) NOT NULL,
  `tipologia` int(11) NOT NULL,
  PRIMARY KEY (`objectid`),
  KEY `tipologia` (`tipologia`),
  CONSTRAINT `punto_di_interesse_ibfk_1` FOREIGN KEY (`tipologia`) REFERENCES `tipologia` (`idTipologia`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;
-- --------------------------------------------------------
--
-- Table structure for table `coordinata`
--

CREATE TABLE IF NOT EXISTS `coordinata` (
  `objectid` int(11) NOT NULL,
  `latitudine` varchar(24) NOT NULL,
  `longitudine` varchar(24) NOT NULL,
  PRIMARY KEY (`objectid`),
  CONSTRAINT `coordinata_ibfk_1` FOREIGN KEY (`objectid`) REFERENCES `punto_di_interesse` (`objectid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;
-- --------------------------------------------------------
--
-- Table structure for table `info_fermata`
--

CREATE TABLE IF NOT EXISTS `info_fermata` (
  `objectid` int(11) NOT NULL,
  `gestore` varchar(34) NOT NULL,
  `linea` varchar(73) NOT NULL,
  PRIMARY KEY (`objectid`),
  CONSTRAINT `info_fermata_ibfk_1` FOREIGN KEY (`objectid`) REFERENCES `punto_di_interesse` (`objectid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;
-- --------------------------------------------------------
--
-- Table structure for table `info_museo`
--

CREATE TABLE IF NOT EXISTS `info_museo` (
  `nome` varchar(100) NOT NULL,
  `gobalid` varchar(38) NOT NULL,
  `tipo` int(5) NOT NULL,
  `link` varchar(101) NOT NULL,
  `objectid` int(11) NOT NULL,
  PRIMARY KEY (`objectid`),
  CONSTRAINT `info_museo_ibfk_1` FOREIGN KEY (`objectid`) REFERENCES `punto_di_interesse` (`objectid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;
-- --------------------------------------------------------
--
-- Table structure for table `percorso_escursionistico`
--

CREATE TABLE IF NOT EXISTS `percorso_escursionistico` (
  `objectid` int(11) NOT NULL,
  `id_percorso` varchar(36) NOT NULL,
  `localita` varchar(255) NOT NULL,
  `difficolta` varchar(19) NOT NULL,
  `nome_numero` varchar(22) NOT NULL,
  `sigla` varchar(10) NOT NULL,
  `dislivello_salita` int(11) NOT NULL,
  `dislivello_discesa` int(11) NOT NULL,
  `lunghezza` int(11) NOT NULL,
  `gestore` varchar(65) NOT NULL,
  `segnavia` varchar(54) NOT NULL,
  `tempo_andata` varchar(8) NOT NULL,
  `tempo_ritorno` varchar(8) NOT NULL,
  `link_google` varchar(220) NOT NULL,
  `link` varchar(175) NOT NULL,
  `altro_segnavia` varchar(125) NOT NULL,
  PRIMARY KEY (`objectid`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;
COMMIT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */
;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */
;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */
;