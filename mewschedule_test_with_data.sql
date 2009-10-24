# Sequel Pro dump
# Version 1191
# http://code.google.com/p/sequel-pro
#
# Host: localhost (MySQL 5.1.37)
# Database: mewschedule_test
# Generation Time: 2009-10-24 10:47:26 -0700
# ************************************************************

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table events
# ------------------------------------------------------------

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
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;

LOCK TABLES `events` WRITE;
/*!40000 ALTER TABLE `events` DISABLE KEYS */;
INSERT INTO `events` (`e_eventID`,`e_roomID`,`e_dateStart`,`e_dateEnd`,`e_eventName`,`e_color`,`e_eventDesc`,`e_panelist`)
VALUES
	(1,1,'2009-12-30 12:00:00','2009-12-30 14:00:00','Test 1','#FF0000','This is a 2-hour event being held on 2009-10-20 starting at 12:00 and going till 14:00, in r_roomID 0.','Admin'),
	(2,1,'2009-12-30 14:00:00','2009-12-30 16:30:00','another fairly long title for T2','#CCCCCC','','Kaku'),
	(3,2,'2009-12-30 14:00:00','2009-12-30 18:00:00','Test 3','#0066FF','this is a description for test 3','Admin'),
	(4,5,'2009-12-30 16:00:00','2009-12-30 17:30:00','yet more wasted space with Test 4','#FFCA52','Description for Test 4','Kitsune'),
	(5,4,'2009-12-30 09:00:00','2009-12-30 10:00:00','obscenely and gratuitously long title involving a sql test for T5','#00FF00','','Admin'),
	(6,3,'2009-12-30 22:00:00','2009-12-31 01:30:00','Late Night Test 6','#0066FF','Test to make sure going from previous day to next works as expected.','Kaku');

/*!40000 ALTER TABLE `events` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table rooms
# ------------------------------------------------------------

DROP TABLE IF EXISTS `rooms`;

CREATE TABLE `rooms` (
  `r_roomID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `r_roomName` tinytext NOT NULL,
  PRIMARY KEY (`r_roomID`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;

LOCK TABLES `rooms` WRITE;
/*!40000 ALTER TABLE `rooms` DISABLE KEYS */;
INSERT INTO `rooms` (`r_roomID`,`r_roomName`)
VALUES
	(1,'Main Events'),
	(2,'Panel Room 1'),
	(3,'Panel Room 2'),
	(4,'Creations Lab'),
	(5,'Exhibition Hall'),
	(6,'Library'),
	(7,'Game Room');

/*!40000 ALTER TABLE `rooms` ENABLE KEYS */;
UNLOCK TABLES;





/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
