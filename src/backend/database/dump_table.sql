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
-- Table `identifier`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `identifier` (
  `objectId` INT(11) NOT NULL,
  `idPoi` CHAR(36) NOT NULL,
  PRIMARY KEY (`objectId`)
) ENGINE = InnoDB DEFAULT CHARACTER SET = utf8mb4;
-- -----------------------------------------------------
-- Table `coordinata`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `coordinata` (
  `idCoordinata` INT(11) NOT NULL AUTO_INCREMENT,
  `latitudine` VARCHAR(24) NOT NULL,
  `longitudine` VARCHAR(24) NOT NULL,
  `objectId` INT(11) NOT NULL,
  PRIMARY KEY (`idCoordinata`),
  CONSTRAINT `coordinata_ibfk_1` FOREIGN KEY (`objectId`) REFERENCES `identifier` (`objectId`) ON DELETE CASCADE ON UPDATE CASCADE
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
  `objectId` INT(11) NOT NULL,
  `descrizione` VARCHAR(255) NULL DEFAULT NULL,
  `tipologia` INT(11) NOT NULL,
  PRIMARY KEY (`objectId`),
  CONSTRAINT `punto_di_interesse_ibfk_1` FOREIGN KEY (`tipologia`) REFERENCES `tipologia` (`idTipologia`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `punto_di_interesse_ibfk_2` FOREIGN KEY (`objectId`) REFERENCES `identifier` (`objectId`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB DEFAULT CHARACTER SET = utf8mb4;
-- -----------------------------------------------------
-- Table `info_fermata`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `info_fermata` (
  `objectId` INT(11) NOT NULL,
  `gestore` VARCHAR(34) NOT NULL,
  `linea` VARCHAR(73) NOT NULL,
  PRIMARY KEY (`objectId`),
  CONSTRAINT `info_fermata_ibfk_1` FOREIGN KEY (`objectId`) REFERENCES `punto_di_interesse` (`objectId`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB DEFAULT CHARACTER SET = utf8mb4;
-- -----------------------------------------------------
-- Table `info_museo`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `info_museo` (
  `objectId` INT(11) NOT NULL,
  `nome` VARCHAR(100) NOT NULL,
  `globalId` VARCHAR(38) NOT NULL,
  `link` VARCHAR(101) NULL DEFAULT NULL,
  PRIMARY KEY (`objectId`),
  CONSTRAINT `info_museo_ibfk_1` FOREIGN KEY (`objectId`) REFERENCES `punto_di_interesse` (`objectId`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB DEFAULT CHARACTER SET = utf8mb4;
-- -----------------------------------------------------
-- Table `percorso_escursionistico`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `percorso_escursionistico` (
  `objectId` INT(11) NOT NULL,
  `localita` VARCHAR(255) NOT NULL,
  `difficolta` VARCHAR(19) NOT NULL,
  `nome_numero` VARCHAR(22) NOT NULL,
  `sigla` VARCHAR(10) NOT NULL,
  `dislivello_salita` INT(11) NOT NULL,
  `dislivello_discesa` INT(11) NOT NULL,
  `lunghezza` INT(11) NOT NULL,
  `gestore` VARCHAR(65) NOT NULL,
  `segnavia` VARCHAR(54) NOT NULL,
  `tempo_andata` VARCHAR(8) NOT NULL,
  `tempo_ritorno` VARCHAR(8) NOT NULL,
  `link_google` VARCHAR(220) NOT NULL,
  `link` VARCHAR(175) NOT NULL,
  `altro_segnavia` VARCHAR(125) NOT NULL,
  PRIMARY KEY (`objectId`),
  CONSTRAINT `percorso_escursionistico_ibfk_1` FOREIGN KEY (`objectId`) REFERENCES `identifier` (`objectId`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB DEFAULT CHARACTER SET = utf8mb4;
COMMIT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */
;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */
;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */
;