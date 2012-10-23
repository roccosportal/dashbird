-- phpMyAdmin SQL Dump
-- version 3.5.2.2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Oct 23, 2012 at 07:03 PM
-- Server version: 5.5.24-0ubuntu0.12.04.1
-- PHP Version: 5.3.10-1ubuntu3.4

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `dashbird`
--

-- --------------------------------------------------------

--
-- Table structure for table `Comments`
--

DROP TABLE IF EXISTS `Comments`;
CREATE TABLE IF NOT EXISTS `Comments` (
  `CommentId` int(11) NOT NULL AUTO_INCREMENT,
  `UserId` int(11) NOT NULL,
  `DashboardEntryId` int(11) NOT NULL,
  `Text` text NOT NULL,
  `DateTime` datetime NOT NULL,
  PRIMARY KEY (`CommentId`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=7 ;

-- --------------------------------------------------------

--
-- Table structure for table `DashboardEntries`
--

DROP TABLE IF EXISTS `DashboardEntries`;
CREATE TABLE IF NOT EXISTS `DashboardEntries` (
  `DashboardEntryId` int(11) NOT NULL AUTO_INCREMENT,
  `UserId` int(11) NOT NULL,
  `Module` text NOT NULL,
  `ReferenceId` int(11) NOT NULL,
  `DateTime` datetime NOT NULL,
  `searchhelper` text NOT NULL,
  PRIMARY KEY (`DashboardEntryId`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=47 ;

-- --------------------------------------------------------

--
-- Table structure for table `DashboardEntriesTags`
--

DROP TABLE IF EXISTS `DashboardEntriesTags`;
CREATE TABLE IF NOT EXISTS `DashboardEntriesTags` (
  `dashboardentriestagsid` int(11) NOT NULL AUTO_INCREMENT,
  `dashboardentryid` int(11) NOT NULL,
  `tagid` int(11) NOT NULL,
  PRIMARY KEY (`dashboardentriestagsid`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=144 ;

-- --------------------------------------------------------

--
-- Table structure for table `EntryShares`
--

DROP TABLE IF EXISTS `EntryShares`;
CREATE TABLE IF NOT EXISTS `EntryShares` (
  `EntryShareId` int(11) NOT NULL AUTO_INCREMENT,
  `DashboardEntryId` int(11) NOT NULL,
  `UserId` int(11) NOT NULL,
  PRIMARY KEY (`EntryShareId`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=13 ;

-- --------------------------------------------------------

--
-- Table structure for table `Links`
--

DROP TABLE IF EXISTS `Links`;
CREATE TABLE IF NOT EXISTS `Links` (
  `LinkId` int(11) NOT NULL AUTO_INCREMENT,
  `Link` text NOT NULL,
  `IsImage` tinyint(1) NOT NULL,
  `UserId` int(11) NOT NULL,
  PRIMARY KEY (`LinkId`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

-- --------------------------------------------------------

--
-- Table structure for table `Notes`
--

DROP TABLE IF EXISTS `Notes`;
CREATE TABLE IF NOT EXISTS `Notes` (
  `NoteId` int(11) NOT NULL AUTO_INCREMENT,
  `Text` text NOT NULL,
  `UserId` int(11) NOT NULL,
  PRIMARY KEY (`NoteId`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=35 ;

-- --------------------------------------------------------

--
-- Table structure for table `SearchhelperParts`
--

DROP TABLE IF EXISTS `SearchhelperParts`;
CREATE TABLE IF NOT EXISTS `SearchhelperParts` (
  `searchhelperpartid` int(11) NOT NULL AUTO_INCREMENT,
  `dashboardentryid` int(11) NOT NULL,
  `keyword` text NOT NULL,
  `startat` int(11) NOT NULL,
  `endat` int(11) NOT NULL,
  PRIMARY KEY (`searchhelperpartid`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=83 ;

-- --------------------------------------------------------

--
-- Table structure for table `Tags`
--

DROP TABLE IF EXISTS `Tags`;
CREATE TABLE IF NOT EXISTS `Tags` (
  `tagId` int(11) NOT NULL AUTO_INCREMENT,
  `title` text NOT NULL,
  PRIMARY KEY (`tagId`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=14 ;

-- --------------------------------------------------------

--
-- Table structure for table `Todos`
--

DROP TABLE IF EXISTS `Todos`;
CREATE TABLE IF NOT EXISTS `Todos` (
  `TodoId` int(11) NOT NULL AUTO_INCREMENT,
  `Text` text NOT NULL,
  `IsDone` tinyint(1) NOT NULL,
  `UserId` int(11) NOT NULL,
  PRIMARY KEY (`TodoId`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=8 ;

-- --------------------------------------------------------

--
-- Table structure for table `Users`
--

DROP TABLE IF EXISTS `Users`;
CREATE TABLE IF NOT EXISTS `Users` (
  `UserId` int(11) NOT NULL AUTO_INCREMENT,
  `Name` text NOT NULL,
  `Password` text NOT NULL,
  PRIMARY KEY (`UserId`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

-- --------------------------------------------------------

--
-- Table structure for table `UserShares`
--

DROP TABLE IF EXISTS `UserShares`;
CREATE TABLE IF NOT EXISTS `UserShares` (
  `UserShareId` int(11) NOT NULL AUTO_INCREMENT,
  `UserId` int(11) NOT NULL,
  `ConnectedUserId` int(11) NOT NULL,
  PRIMARY KEY (`UserShareId`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=7 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;