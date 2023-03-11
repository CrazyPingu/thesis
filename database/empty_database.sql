-- phpMyAdmin SQL Dump
-- version 5.0.4deb2+deb11u1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Creato il: Mar 11, 2023 alle 15:40
-- Versione del server: 10.5.18-MariaDB-0+deb11u1
-- Versione PHP: 8.0.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `tirocinio`
--

-- --------------------------------------------------------

--
-- Struttura della tabella `coordinata`
--

CREATE TABLE `coordinata` (
  `objectid` int(11) NOT NULL,
  `latitudine` varchar(24) NOT NULL,
  `longitudine` varchar(24) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `info_fermata`
--

CREATE TABLE `info_fermata` (
  `objectid` int(11) NOT NULL,
  `gestore` varchar(34) NOT NULL,
  `linea` varchar(73) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `info_museo`
--

CREATE TABLE `info_museo` (
  `nome` varchar(100) NOT NULL,
  `gobalid` varchar(38) NOT NULL,
  `tipo` int(5) NOT NULL,
  `link` varchar(101) NOT NULL,
  `objectid` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `percorso_escursionistico`
--

CREATE TABLE `percorso_escursionistico` (
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
  `altro_segnavia` varchar(125) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `punto_di_interesse`
--

CREATE TABLE `punto_di_interesse` (
  `objectid` int(11) NOT NULL,
  `id_poi` varchar(36) NOT NULL,
  `descrizione` varchar(255) NOT NULL,
  `tipologia` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `tipologia`
--

CREATE TABLE `tipologia` (
  `Nome` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indici per le tabelle scaricate
--

--
-- Indici per le tabelle `coordinata`
--
ALTER TABLE `coordinata`
  ADD PRIMARY KEY (`objectid`);

--
-- Indici per le tabelle `info_fermata`
--
ALTER TABLE `info_fermata`
  ADD PRIMARY KEY (`objectid`);

--
-- Indici per le tabelle `info_museo`
--
ALTER TABLE `info_museo`
  ADD PRIMARY KEY (`objectid`);

--
-- Indici per le tabelle `punto_di_interesse`
--
ALTER TABLE `punto_di_interesse`
  ADD PRIMARY KEY (`objectid`),
  ADD KEY `tipologia` (`tipologia`);

--
-- Indici per le tabelle `tipologia`
--
ALTER TABLE `tipologia`
  ADD PRIMARY KEY (`Nome`);

--
-- Limiti per le tabelle scaricate
--

--
-- Limiti per la tabella `coordinata`
--
ALTER TABLE `coordinata`
  ADD CONSTRAINT `coordinata_ibfk_1` FOREIGN KEY (`objectid`) REFERENCES `punto_di_interesse` (`objectid`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limiti per la tabella `info_fermata`
--
ALTER TABLE `info_fermata`
  ADD CONSTRAINT `info_fermata_ibfk_1` FOREIGN KEY (`objectid`) REFERENCES `punto_di_interesse` (`objectid`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limiti per la tabella `info_museo`
--
ALTER TABLE `info_museo`
  ADD CONSTRAINT `info_museo_ibfk_1` FOREIGN KEY (`objectid`) REFERENCES `punto_di_interesse` (`objectid`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limiti per la tabella `punto_di_interesse`
--
ALTER TABLE `punto_di_interesse`
  ADD CONSTRAINT `punto_di_interesse_ibfk_1` FOREIGN KEY (`tipologia`) REFERENCES `tipologia` (`Nome`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
