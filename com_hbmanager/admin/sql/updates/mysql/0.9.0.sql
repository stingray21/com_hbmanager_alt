--
-- Table structure for table `hb_mannschaft`
--

CREATE TABLE IF NOT EXISTS `hb_mannschaft` (
  `mannschaftID` int(3) NOT NULL AUTO_INCREMENT,
  `reihenfolge` int(3) DEFAULT NULL,
  `kuerzel` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `mannschaft` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `name` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `nameKurz` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ligaKuerzel` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `liga` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `geschlecht` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `jugend` tinyint(1) DEFAULT NULL,
  `hvwLink` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `updateTabelle` datetime DEFAULT NULL,
  `updateSpielplan` datetime DEFAULT NULL,
  PRIMARY KEY (`mannschaftID`),
  UNIQUE KEY `kuerzel` (`kuerzel`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=630 ;

-- --------------------------------------------------------

--
-- Table structure for table `hb_spiel`
--

CREATE TABLE IF NOT EXISTS `hb_spiel` (
  `spielID` int(6) NOT NULL AUTO_INCREMENT,
  `spielIDhvw` int(6) DEFAULT NULL,
  `kuerzel` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `hallenNummer` int(6) DEFAULT NULL,
  `datum` date DEFAULT NULL,
  `uhrzeit` time DEFAULT NULL,
  `heim` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `gast` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `toreHeim` int(3) DEFAULT NULL,
  `toreGast` int(3) DEFAULT NULL,
  `bemerkung` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`spielID`),
  UNIQUE KEY `spielIDhvw` (`spielIDhvw`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=189 ;

-- --------------------------------------------------------

--
-- Table structure for table `hb_spielbericht`
--

CREATE TABLE IF NOT EXISTS `hb_spielbericht` (
  `spielberichtID` int(6) NOT NULL AUTO_INCREMENT,
  `spielIDhvw` int(6) DEFAULT NULL,
  `bericht` longtext COLLATE utf8_unicode_ci,
  `spielerliste` longtext COLLATE utf8_unicode_ci,
  `zusatz` longtext COLLATE utf8_unicode_ci,
  `halbzeitstand` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `spielverlauf` mediumtext COLLATE utf8_unicode_ci,
  PRIMARY KEY (`spielberichtID`),
  UNIQUE KEY `spielIDhvw` (`spielIDhvw`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=83 ;

-- --------------------------------------------------------

--
-- Table structure for table `hb_spielvorschau`
--

CREATE TABLE IF NOT EXISTS `hb_spielvorschau` (
  `spielvorschauID` int(6) NOT NULL AUTO_INCREMENT,
  `spielIDhvw` int(6) DEFAULT NULL,
  `vorschau` longtext COLLATE utf8_unicode_ci,
  `treffOrt` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `treffZeit` time DEFAULT NULL,
  PRIMARY KEY (`spielvorschauID`),
  UNIQUE KEY `spielIDhvw` (`spielIDhvw`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=80 ;

-- --------------------------------------------------------

--
-- Table structure for table `hb_staffel`
--

CREATE TABLE IF NOT EXISTS `hb_staffel` (
  `staffelID` int(3) NOT NULL AUTO_INCREMENT,
  `staffel` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `staffelName` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `staffelLink` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `geschlecht` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `jugend` tinyint(1) DEFAULT NULL,
  `saison` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `rankingTeams` varchar(1000) COLLATE utf8_unicode_ci DEFAULT NULL,
  `scheduleTeams` varchar(1000) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`staffelID`),
  UNIQUE KEY `staffel` (`staffel`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=40 ;

-- --------------------------------------------------------

--
-- Table structure for table `hb_tabelle`
--

CREATE TABLE IF NOT EXISTS `hb_tabelle` (
  `ID` int(2) unsigned NOT NULL AUTO_INCREMENT,
  `kuerzel` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `platz` tinyint(2) DEFAULT NULL,
  `verein` varchar(60) COLLATE utf8_unicode_ci DEFAULT NULL,
  `spiele` tinyint(2) DEFAULT NULL,
  `siege` tinyint(2) DEFAULT NULL,
  `unentschieden` tinyint(2) DEFAULT NULL,
  `niederlagen` tinyint(2) DEFAULT NULL,
  `plustore` mediumint(4) DEFAULT NULL,
  `minustore` mediumint(4) DEFAULT NULL,
  `torDifferenz` mediumint(4) DEFAULT NULL,
  `pluspunkte` tinyint(2) DEFAULT NULL,
  `minuspunkte` tinyint(2) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=236 ;

-- --------------------------------------------------------

--
-- Table structure for table `hb_halle`
--

CREATE TABLE IF NOT EXISTS `hb_halle` (
  `halleID` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `hallenNummer` int(6) DEFAULT NULL,
  `kurzname` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `name` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `land` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `plz` int(6) DEFAULT NULL,
  `stadt` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `strasse` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `telefon` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `bezirkNummer` int(6) DEFAULT NULL,
  `bezirk` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `freigabeVerband` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `freigabeBezirk` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `haftmittel` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `letzteAenderung` datetime DEFAULT NULL,
  PRIMARY KEY (`halleID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=54 ;
