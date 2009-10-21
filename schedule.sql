-- MySQL dump 10.11
--
-- Host: localhost    Database: 
-- ------------------------------------------------------
-- Server version	5.0.67-0ubuntu6

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Current Database: `mewschedule`
--

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `mewschedule` /*!40100 DEFAULT CHARACTER SET latin1 */;

USE `mewschedule`;

--
-- Table structure for table `events`
--

DROP TABLE IF EXISTS `events`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `events` (
  `e_eventID` int(11) NOT NULL auto_increment,
  `e_roomID` int(11) NOT NULL,
  `e_day` int(11) NOT NULL,
  `e_start` int(11) NOT NULL,
  `e_end` int(11) NOT NULL,
  `e_eventname` tinytext NOT NULL,
  `e_color` tinytext NOT NULL,
  `e_desc` text NOT NULL,
  `e_panelist` tinytext NOT NULL,
  PRIMARY KEY  (`e_eventID`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `events`
--

LOCK TABLES `events` WRITE;
/*!40000 ALTER TABLE `events` DISABLE KEYS */;
INSERT INTO `events` VALUES (1,1,1,17,20,'Opening Ceremonies','#C0C0C0','This is the opening ceremony for MEWcon','kitsune'),(2,1,2,29,32,'Cosplay Choreography','#C0C0C0','',''),(3,1,2,34,37,'Cosplay Contest','#C0C0C0','','');
/*!40000 ALTER TABLE `events` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rooms`
--

DROP TABLE IF EXISTS `rooms`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `rooms` (
  `r_roomID` int(11) NOT NULL auto_increment,
  `r_roomname` tinytext NOT NULL,
  PRIMARY KEY  (`r_roomID`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `rooms`
--

LOCK TABLES `rooms` WRITE;
/*!40000 ALTER TABLE `rooms` DISABLE KEYS */;
INSERT INTO `rooms` VALUES (1,'Main Events'),(2,'Panel Room 1'),(3,'Panel Room 2'),(4,'Creations Lab'),(5,'Exhibitors Hall'),(6,'Library');
/*!40000 ALTER TABLE `rooms` ENABLE KEYS */;
UNLOCK TABLES;
