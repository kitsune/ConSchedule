CREATE DATABASE IF NOT EXISTS `mewschedule_test`;
USE `mewschedule_test`;

--
-- Table structure for table `events`
--

DROP TABLE IF EXISTS `events`;

CREATE TABLE `events` (
  `e_eventID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `e_roomID` int(10) unsigned NOT NULL,
  `e_dateStart` datetime NOT NULL,
  `e_dateEnd` datetime NOT NULL,
  `e_eventName` tinytext NOT NULL,
  `e_color` tinytext NOT NULL,
  `e_eventDesc` text NOT NULL,
  `e_panelist` tinytext NOT NULL,
  PRIMARY KEY (`e_eventID`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;

--
-- Table structure for table `rooms`
--

DROP TABLE IF EXISTS `rooms`;

CREATE TABLE `rooms` (
  `r_roomID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `r_roomName` tinytext NOT NULL,
  PRIMARY KEY (`r_roomID`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;