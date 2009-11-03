# Sequel Pro dump
# Version 1191
# http://code.google.com/p/sequel-pro
#
# Host: localhost (MySQL 5.1.37)
# Database: mewschedule_test
# Generation Time: 2009-11-03 00:13:09 -0800
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
) ENGINE=MyISAM AUTO_INCREMENT=76 DEFAULT CHARSET=latin1;

LOCK TABLES `events` WRITE;
/*!40000 ALTER TABLE `events` DISABLE KEYS */;
INSERT INTO `events` (`e_eventID`,`e_roomID`,`e_dateStart`,`e_dateEnd`,`e_eventName`,`e_color`,`e_eventDesc`,`e_panelist`)
VALUES
	(1,1,'2009-12-31 12:00:00','2009-12-31 14:00:00','Event Test 1: The Beginning.','#FF0000','In the beginning, Japan created Anime while the West created Fantasy/Sci-Fi, and it was decent.\\nThen Admin descended upon the world and gave us MEWcon, and it was awesome.','Admin'),
	(2,1,'2009-12-31 14:00:00','2009-12-31 16:30:00','Event Test 2, with a fairly long name, but no description.','#CCCCCC','','Kaku'),
	(3,2,'2009-12-31 14:00:00','2009-12-31 18:00:00','Event Test 3: Revenge of the Event Test.','#0066FF','You remember him from Events 1 and 2, now Event Test returns in a cinematographic exercise in unbelievability.','Admin'),
	(4,5,'2009-12-31 16:00:00','2009-12-31 17:30:00','Event Test 4, which also wastes some space with its name.','#FFCA52','A non-witty description for Event Test 4.','Kitsune'),
	(5,4,'2009-12-31 15:30:00','2009-12-31 18:30:00','Event Test 5, which has an obscenely long name and a less equally long description. That is to say, it has no description at all.','#00FF00','','Admin'),
	(6,3,'2009-12-31 22:00:00','2010-01-01 01:30:00','Event Test 6: For the Night Owls.','#0066FF','Prowl the night with this Event. AKA: The test that makes sure pre-midnight to post-midnight events work as expected.','Kaku'),
	(7,7,'2009-12-31 08:00:00','2009-12-31 12:00:00','Event Test 7: Gamer Edition.','#FF00FF','Join Event on an epic journey to save the princess, who fails to put out just like the last 6 games!','Legoman'),
	(8,6,'2009-12-31 08:00:00','2009-12-31 15:00:00','Event Test 8: Pocket Protector Power (P3).','#CCCCCC','Don\'t know where that book is, mate. There\'s a lot of them about!','Kitsune'),
	(9,2,'2009-12-31 10:00:00','2009-12-31 13:30:00','Event Test 9','#777777','Go Go Gadget Event Test!','Kai'),
	(10,3,'2009-12-31 08:30:00','2009-12-31 09:00:00','Event Test 10','#7777CC','','Admin'),
	(11,4,'2009-12-31 08:00:00','2009-12-31 11:30:00','Event Test 11','#CC77CC','','TOFU'),
	(12,3,'2009-12-31 09:30:00','2009-12-31 10:30:00','Event Test 12','#FFFF00','','Legoman'),
	(13,7,'2009-12-31 12:30:00','2009-12-31 15:30:00','Event Test 13','#5A5A5A','This little Event went to market; this little Event stayed home; and this little Event went \"OMG stop poking my kidneys!\"','Kaku'),
	(14,5,'2009-12-31 08:00:00','2009-12-31 11:00:00','Event Test 14','#CCFFCC','',''),
	(15,5,'2009-12-31 11:00:00','2009-12-31 12:30:00','Event Test 15','#FFCCCC','',''),
	(16,3,'2009-12-31 11:00:00','2009-12-31 12:30:00','Event Test 16','#CCCCFF','',''),
	(17,3,'2009-12-31 13:00:00','2009-12-31 16:00:00','Event Test 17','#CCFFCC','',''),
	(18,4,'2009-12-31 11:30:00','2009-12-31 15:00:00','Event Test 18','#00CC00','',''),
	(19,1,'2009-12-31 08:00:00','2009-12-31 11:30:00','Event Test 19','#CC0000','',''),
	(20,5,'2009-12-31 13:00:00','2009-12-31 15:00:00','Event Test 20','#0000CC','',''),
	(21,2,'2009-12-31 08:30:00','2009-12-31 09:30:00','Event Test 21','#CCCC00','',''),
	(22,6,'2009-12-31 16:30:00','2009-12-31 18:30:00','Event Test 22','#00CCCC','',''),
	(23,7,'2009-12-31 16:30:00','2009-12-31 20:00:00','Event Test 23','#CC00CC','',''),
	(24,1,'2009-12-31 17:00:00','2009-12-31 19:30:00','Event Test 24','#C0C0C0','',''),
	(25,3,'2009-12-31 17:30:00','2009-12-31 19:00:00','Event Test 25','#CC99FF','',''),
	(26,1,'2010-01-01 12:00:00','2010-01-01 14:00:00','D2 Event Test 1: The Beginning.','#FF0000','In the beginning, Japan created Anime while the West created Fantasy/Sci-Fi, and it was decent.\\nThen Admin descended upon the world and gave us MEWcon, and it was awesome.','Admin'),
	(27,1,'2010-01-01 14:00:00','2010-01-01 16:30:00','D2 Event Test 2, with a fairly long name, but no description.','#CCCCCC','','Kaku'),
	(28,2,'2010-01-01 14:00:00','2010-01-01 18:00:00','D2 Event Test 3: Revenge of the D2 Event Test.','#0066FF','You remember him from Events 1 and 2, now D2 Event Test returns in a cinematographic exercise in unbelievability.','Admin'),
	(29,5,'2010-01-01 16:00:00','2010-01-01 17:30:00','D2 Event Test 4, which also wastes some space with its name.','#FFCA52','A non-witty description for D2 Event Test 4.','Kitsune'),
	(30,4,'2010-01-01 15:30:00','2010-01-01 18:30:00','D2 Event Test 5, which has an obscenely long name and a less equally long description. That is to say, it has no description at all.','#00FF00','','Admin'),
	(31,3,'2010-01-01 22:00:00','2010-01-02 01:30:00','D2 Event Test 6: For the Night Owls.','#0066FF','Prowl the night with this Event. AKA: The test that makes sure pre-midnight to post-midnight events work as expected.','Kaku'),
	(32,7,'2010-01-01 08:00:00','2010-01-01 12:00:00','D2 Event Test 7: Gamer Edition.','#FF00FF','Join Event on an epic journey to save the princess, who fails to put out just like the last 6 games!','Legoman'),
	(33,6,'2010-01-01 08:00:00','2010-01-01 15:00:00','D2 Event Test 8: Pocket Protector Power (P3).','#CCCCCC','Don\'t know where that book is, mate. There\'s a lot of them about!','Kitsune'),
	(34,2,'2010-01-01 10:00:00','2010-01-01 13:30:00','D2 Event Test 9','#777777','Go Go Gadget D2 Event Test!','Kai'),
	(35,3,'2010-01-01 08:30:00','2010-01-01 09:00:00','D2 Event Test 10','#7777CC','','Admin'),
	(36,4,'2010-01-01 08:00:00','2010-01-01 11:30:00','D2 Event Test 11','#CC77CC','','TOFU'),
	(37,3,'2010-01-01 09:30:00','2010-01-01 10:30:00','D2 Event Test 12','#FFFF00','','Legoman'),
	(38,7,'2010-01-01 12:30:00','2010-01-01 15:30:00','D2 Event Test 13','#5A5A5A','This little Event went to market; this little Event stayed home; and this little Event went \"OMG stop poking my kidneys!\"','Kaku'),
	(39,5,'2010-01-01 08:00:00','2010-01-01 11:00:00','D2 Event Test 14','#CCFFCC','',''),
	(40,5,'2010-01-01 11:00:00','2010-01-01 12:30:00','D2 Event Test 15','#FFCCCC','',''),
	(41,3,'2010-01-01 11:00:00','2010-01-01 12:30:00','D2 Event Test 16','#CCCCFF','',''),
	(42,3,'2010-01-01 13:00:00','2010-01-01 15:30:00','D2 Event Test 17','#CCFFCC','',''),
	(43,4,'2010-01-01 11:30:00','2010-01-01 15:00:00','D2 Event Test 18','#00CC00','',''),
	(44,1,'2010-01-01 08:00:00','2010-01-01 11:30:00','D2 Event Test 19','#CC0000','',''),
	(45,5,'2010-01-01 13:00:00','2010-01-01 15:00:00','D2 Event Test 20','#0000CC','',''),
	(46,2,'2010-01-01 08:30:00','2010-01-01 09:30:00','D2 Event Test 21','#CCCC00','',''),
	(47,6,'2010-01-01 16:30:00','2010-01-01 18:30:00','D2 Event Test 22','#00CCCC','',''),
	(48,7,'2010-01-01 16:30:00','2010-01-01 20:00:00','D2 Event Test 23','#CC00CC','',''),
	(49,1,'2010-01-01 17:00:00','2010-01-01 19:30:00','D2 Event Test 24','#C0C0C0','',''),
	(50,3,'2010-01-01 17:30:00','2010-01-01 19:00:00','D2 Event Test 25','#CC99FF','',''),
	(51,1,'2010-01-02 12:00:00','2010-01-02 14:00:00','D3 Event Test 1: The Beginning.','#FF0000','In the beginning, Japan created Anime while the West created Fantasy/Sci-Fi, and it was decent.\\nThen Admin descended upon the world and gave us MEWcon, and it was awesome.','Admin'),
	(52,1,'2010-01-02 14:00:00','2010-01-02 16:30:00','D3 Event Test 2, with a fairly long name, but no description.','#CCCCCC','','Kaku'),
	(53,2,'2010-01-02 14:00:00','2010-01-02 18:00:00','D3 Event Test 3: Revenge of the D3 Event Test.','#0066FF','You remember him from Events 1 and 2, now D3 Event Test returns in a cinematographic exercise in unbelievability.','Admin'),
	(54,5,'2010-01-02 16:00:00','2010-01-02 17:30:00','D3 Event Test 4, which also wastes some space with its name.','#FFCA52','A non-witty description for D3 Event Test 4.','Kitsune'),
	(55,4,'2010-01-02 15:30:00','2010-01-02 18:30:00','D3 Event Test 5, which has an obscenely long name and a less equally long description. That is to say, it has no description at all.','#00FF00','','Admin'),
	(56,3,'2010-01-02 22:00:00','2010-01-03 00:00:00','D3 Event Test 6: For the Night Owls.','#0066FF','Prowl the night with this Event. AKA: The test that makes sure pre-midnight to post-midnight events work as expected.','Kaku'),
	(57,7,'2010-01-02 08:00:00','2010-01-02 12:00:00','D3 Event Test 7: Gamer Edition.','#FF00FF','Join Event on an epic journey to save the princess, who fails to put out just like the last 6 games!','Legoman'),
	(58,6,'2010-01-02 08:00:00','2010-01-02 15:00:00','D3 Event Test 8: Pocket Protector Power (P3).','#CCCCCC','Don\'t know where that book is, mate. There\'s a lot of them about!','Kitsune'),
	(59,2,'2010-01-02 10:00:00','2010-01-02 13:30:00','D3 Event Test 9','#777777','Go Go Gadget D3 Event Test!','Kai'),
	(60,3,'2010-01-02 08:30:00','2010-01-02 09:00:00','D3 Event Test 10','#7777CC','','Admin'),
	(61,4,'2010-01-02 08:00:00','2010-01-02 11:30:00','D3 Event Test 11','#CC77CC','','TOFU'),
	(62,3,'2010-01-02 09:30:00','2010-01-02 10:30:00','D3 Event Test 12','#FFFF00','','Legoman'),
	(63,7,'2010-01-02 12:30:00','2010-01-02 15:30:00','D3 Event Test 13','#5A5A5A','This little Event went to market; this little Event stayed home; and this little Event went \"OMG stop poking my kidneys!\"','Kaku'),
	(64,5,'2010-01-02 08:00:00','2010-01-02 11:00:00','D3 Event Test 14','#CCFFCC','',''),
	(65,5,'2010-01-02 11:00:00','2010-01-02 12:30:00','D3 Event Test 15','#FFCCCC','',''),
	(66,3,'2010-01-02 11:00:00','2010-01-02 12:30:00','D3 Event Test 16','#CCCCFF','',''),
	(67,3,'2010-01-02 13:00:00','2010-01-02 15:30:00','D3 Event Test 17','#CCFFCC','',''),
	(68,4,'2010-01-02 11:30:00','2010-01-02 15:00:00','D3 Event Test 18','#00CC00','',''),
	(69,1,'2010-01-02 08:00:00','2010-01-02 11:30:00','D3 Event Test 19','#CC0000','',''),
	(70,5,'2010-01-02 13:00:00','2010-01-02 15:00:00','D3 Event Test 20','#0000CC','',''),
	(71,2,'2010-01-02 08:30:00','2010-01-02 09:30:00','D3 Event Test 21','#CCCC00','',''),
	(72,6,'2010-01-02 16:30:00','2010-01-02 18:30:00','D3 Event Test 22','#00CCCC','',''),
	(73,7,'2010-01-02 16:30:00','2010-01-02 20:00:00','D3 Event Test 23','#CC00CC','',''),
	(74,1,'2010-01-02 17:00:00','2010-01-02 19:30:00','D3 Event Test 24','#C0C0C0','',''),
	(75,3,'2010-01-02 17:30:00','2010-01-02 19:00:00','D3 Event Test 25','#CC99FF','','');

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


# Dump of table userSchedule
# ------------------------------------------------------------

DROP TABLE IF EXISTS `userSchedule`;

CREATE TABLE `userSchedule` (
  `us_userID` int(11) unsigned NOT NULL DEFAULT '0',
  `us_eventID` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`us_userID`,`us_eventID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

LOCK TABLES `userSchedule` WRITE;
/*!40000 ALTER TABLE `userSchedule` DISABLE KEYS */;
INSERT INTO `userSchedule` (`us_userID`,`us_eventID`)
VALUES
	(1,1),
	(1,5);

/*!40000 ALTER TABLE `userSchedule` ENABLE KEYS */;
UNLOCK TABLES;





/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
