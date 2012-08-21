-- phpMyAdmin SQL Dump
-- version 3.4.5
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Erstellungszeit: 21. Aug 2012 um 22:41
-- Server Version: 5.5.16
-- PHP-Version: 5.3.8

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Datenbank: `dashboard`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `dashboardentries`
--

CREATE TABLE IF NOT EXISTS `dashboardentries` (
  `DashboardEntryId` int(11) NOT NULL AUTO_INCREMENT,
  `UserId` int(11) NOT NULL,
  `Module` text NOT NULL,
  `ReferenceId` int(11) NOT NULL,
  `Date` datetime NOT NULL,
  `searchhelper` text NOT NULL,
  PRIMARY KEY (`DashboardEntryId`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=197 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `dashboardentriestags`
--

CREATE TABLE IF NOT EXISTS `dashboardentriestags` (
  `dashboardentriestagsid` int(11) NOT NULL AUTO_INCREMENT,
  `dashboardentryid` int(11) NOT NULL,
  `tagid` int(11) NOT NULL,
  PRIMARY KEY (`dashboardentriestagsid`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=236 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `links`
--

CREATE TABLE IF NOT EXISTS `links` (
  `LinkId` int(11) NOT NULL AUTO_INCREMENT,
  `Link` text NOT NULL,
  `IsImage` tinyint(1) NOT NULL,
  `UserId` int(11) NOT NULL,
  PRIMARY KEY (`LinkId`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=55 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `notes`
--

CREATE TABLE IF NOT EXISTS `notes` (
  `NoteId` int(11) NOT NULL AUTO_INCREMENT,
  `Text` text NOT NULL,
  `UserId` int(11) NOT NULL,
  PRIMARY KEY (`NoteId`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=77 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `searchhelperparts`
--

CREATE TABLE IF NOT EXISTS `searchhelperparts` (
  `searchhelperpartid` int(11) NOT NULL AUTO_INCREMENT,
  `dashboardentryid` int(11) NOT NULL,
  `keyword` text NOT NULL,
  `startat` int(11) NOT NULL,
  `endat` int(11) NOT NULL,
  PRIMARY KEY (`searchhelperpartid`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=73 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `tags`
--

CREATE TABLE IF NOT EXISTS `tags` (
  `tagId` int(11) NOT NULL AUTO_INCREMENT,
  `title` text NOT NULL,
  PRIMARY KEY (`tagId`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=54 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `todos`
--

CREATE TABLE IF NOT EXISTS `todos` (
  `TodoId` int(11) NOT NULL AUTO_INCREMENT,
  `Text` text NOT NULL,
  `IsDone` tinyint(1) NOT NULL,
  `UserId` int(11) NOT NULL,
  PRIMARY KEY (`TodoId`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=16 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `UserId` int(11) NOT NULL AUTO_INCREMENT,
  `Name` text NOT NULL,
  `Password` text NOT NULL,
  PRIMARY KEY (`UserId`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
