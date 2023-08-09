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
-- Table `identificatore`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `identificatore` (
  `idPoi` CHAR(36) NOT NULL,
  `objectId` INT(11) NOT NULL,
  PRIMARY KEY (`idPoi`)
) ENGINE = InnoDB DEFAULT CHARACTER SET = utf8mb4;
-- -----------------------------------------------------
-- Table `coordinata`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `coordinata` (
  `idCoordinata` INT(11) NOT NULL AUTO_INCREMENT,
  `latitudine` VARCHAR(24) NOT NULL,
  `longitudine` VARCHAR(24) NOT NULL,
  `idPoi` CHAR(36) NOT NULL,
  PRIMARY KEY (`idCoordinata`),
  CONSTRAINT `coordinata_ibfk_1` FOREIGN KEY (`idPoi`) REFERENCES `identificatore` (`idPoi`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;
-- -----------------------------------------------------
-- Table `tipologia`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `tipologia` (
  `idTipologia` INT(11) NOT NULL AUTO_INCREMENT,
  `tipo` VARCHAR(50) NOT NULL,
  PRIMARY KEY (`idTipologia`)
) ENGINE = InnoDB DEFAULT CHARACTER SET = utf8mb4;
-- -----------------------------------------------------
-- Table `punto_di_interesse`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `punto_di_interesse` (
  `idPoi` CHAR(36) NOT NULL,
  `descrizione` VARCHAR(255) NULL DEFAULT NULL,
  `tipologia` INT(11) NOT NULL,
  PRIMARY KEY (`idPoi`),
  CONSTRAINT `punto_di_interesse_ibfk_1` FOREIGN KEY (`tipologia`) REFERENCES `tipologia` (`idTipologia`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `punto_di_interesse_ibfk_2` FOREIGN KEY (`idPoi`) REFERENCES `identificatore` (`idPoi`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB DEFAULT CHARACTER SET = utf8mb4;
-- -----------------------------------------------------
-- Table `info_fermata`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `info_fermata` (
  `idPoi` CHAR(36) NOT NULL,
  `gestore` VARCHAR(34) NOT NULL,
  `linea` VARCHAR(73) NOT NULL,
  PRIMARY KEY (`idPoi`),
  CONSTRAINT `info_fermata_ibfk_1` FOREIGN KEY (`idPoi`) REFERENCES `punto_di_interesse` (`idPoi`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB DEFAULT CHARACTER SET = utf8mb4;
-- -----------------------------------------------------
-- Table `info_museo`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `info_museo` (
  `idPoi` CHAR(36) NOT NULL,
  `nome` VARCHAR(100) NOT NULL,
  `globalId` VARCHAR(38) NOT NULL,
  `link` VARCHAR(101) NULL DEFAULT NULL,
  PRIMARY KEY (`idPoi`),
  CONSTRAINT `info_museo_ibfk_1` FOREIGN KEY (`idPoi`) REFERENCES `punto_di_interesse` (`idPoi`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB DEFAULT CHARACTER SET = utf8mb4;
-- -----------------------------------------------------
-- Table `percorso_escursionistico`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `percorso_escursionistico` (
  `idPercorso` CHAR(36) NOT NULL,
  `localita` VARCHAR(255) NOT NULL,
  `difficolta` VARCHAR(19) NOT NULL,
  `nome_numero` VARCHAR(22) NOT NULL,
  `sigla` VARCHAR(10) NOT NULL,
  `dislivello_salita` INT(11) NOT NULL,
  `dislivello_discesa` INT(11) NOT NULL,
  `lunghezza` INT(11) NOT NULL,
  `gestore` VARCHAR(65) DEFAULT NULL,
  `segnavia` VARCHAR(54) DEFAULT NULL,
  `tempo_andata` VARCHAR(8) DEFAULT NULL,
  `tempo_ritorno` VARCHAR(8) DEFAULT NULL,
  `link_google` VARCHAR(220) DEFAULT NULL,
  `link` VARCHAR(175) DEFAULT NULL,
  `altro_segnavia` VARCHAR(125) DEFAULT NULL,
  PRIMARY KEY (`idPercorso`),
  CONSTRAINT `percorso_escursionistico_ibfk_1` FOREIGN KEY (`idPercorso`) REFERENCES `identificatore` (`idPoi`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;
-- -----------------------------------------------------
-- Table `utenti`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `utente` (
  `idUtente` INT(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `isAdmin` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`idUtente`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -----------------------------------------------------
-- Table `preferiti`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `preferiti` (
  `idPreferiti` int(11) NOT NULL AUTO_INCREMENT,
  `idUtente` int(11) NOT NULL,
  `idPoi` char(36) NOT NULL,
  PRIMARY KEY (`idPreferiti`),
  CONSTRAINT `preferiti_ibfk_1` FOREIGN KEY (`idUtente`) REFERENCES `utente` (`idUtente`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `preferiti_ibfk_2` FOREIGN KEY (`idPoi`) REFERENCES `punto_di_interesse` (`idPoi`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


COMMIT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */
;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */
;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */
;